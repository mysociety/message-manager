<?php

/*-----------------------------------------------------------------
 * Netcast shell commands:
 *
 * netcast gateway_test [source-name/id]
 * netcast get_incoming [source-name/id]
 *
 * Issue with: "cd app & Console/cake netcast gateway_test netcast-gateway"
 *
 *----------------------------------------------------------------*/
class NetcastShell extends AppShell {
	public $uses = array('MessageSource', 'Message', 'Status', 'Action', 'ActionType');
	
	public static $RETRY_LIMIT = 3;

	public function getOptionParser() {
		$source_id_arg_def = array(
			'source_id' => array(
				'help' => __('The id or name of the message souce (gateway)'),
				'required' => true
			)
		);
	    $parser = parent::getOptionParser();
		$parser->addSubcommand('gateway_list', array('help' => __('List available sources.')));
		$parser->addSubcommand('gateway_test', array(
					'help' => __('Test the connection to the gateway (like pinging).'),
					'parser' => array('arguments' => $source_id_arg_def)
				));
		$parser->addSubcommand('get_incoming', array(
					'help' => __('Pull down incoming messages from the gateway and load them into the database.'),
					'parser' => array(
						'options' => array(
							'allow-dups' => array(
								'help' => __('Save messages even if they already seem to be in the database (defaults to false, ' .
											'so duplicate messages will be skipped).'), 
								'boolean' => true,
								'short' => 'a',
								'default' => false
							)
						),
						'arguments' => $source_id_arg_def
					)
				));
				
	/*			
		$parser->addOption('allow-dups', array(
					'help' => __('Save messages even if they already seem to be in the database (defaults to false, ' .
								'so duplicate messages will be skipped).'), 
					'boolean' => true,
					'short' => 'a',
					'default' => false));
	*/
	    return $parser;
	}
	
	public function gateway_test() {
		$source = $this->get_message_source($this->args[0]);
		$ms = $source['MessageSource'];
		$this->out(__("Testing connection to message source \"%s\"", $ms['name']), 1, Shell::VERBOSE);
		$this->check_url($ms);
		$netcast = $this->get_netcast_connection($ms);
		$ret_val = $this->call_netcast_function($netcast, "GETCONNECT", array($ms['remote_id']));
		$ret_val = MessageSource::decode_netcast_retval($ret_val);
		$this->out($ret_val, 1, Shell::QUIET);
	}

	public function gateway_list() {
		$sources = $this->MessageSource->find('all', array('fields' => array('id', 'name', 'url')));
		foreach ($sources as $s) {
			$this->out(sprintf("%4s %24s  %s", "id", "name", "url"), 1, Shell::NORMAL);
			$this->out(sprintf("%4s %24s  %s", "----", "------------------------", "---"), 1, Shell::NORMAL);
			$this->out(sprintf("%4s %24s  %s", $s['MessageSource']['id'], $s['MessageSource']['name'], $s['MessageSource']['url']), 1, Shell::QUIET);
		}
		$this->out(__("Done"), 1, Shell::VERBOSE);
	}

	public function get_incoming() {
		$source = $this->get_message_source($this->args[0]);
		$ms = $source['MessageSource'];
		$this->out(__("Getting incoming messages from message source \"%s\"", $ms['name']), 1, Shell::VERBOSE);
		$this->check_url($ms);
		$netcast = $this->get_netcast_connection($ms);
		$ret_val = $this->call_netcast_function($netcast, "GETINCOMING", array($ms['remote_id']));
		$msgs_received = count($ret_val);
		$msgs_skipped = 0;
		$msgs_saved = 0;
		$msgs_failed = 0;
		if (is_array($ret_val)) {
			$this->out(__("Received incoming messages: %s", $msgs_received), 1, Shell::VERBOSE);
			foreach ($ret_val as $msg) {
				$this->out(__("Processing message: [%s] %s", $msg['min'], $msg['msg']), 1, Shell::VERBOSE);
				// checking for existing messages is a wee bit tricky cos the tag might need to be removed *sigh*
				$conditions = array('Message.from_address' => $msg['min']);
				$tag_data = Message::separate_out_tags($msg['msg']);
				foreach ($tag_data as $key => $value) {
					$conditions["Message.$key"] = $value;
				}
				$existing_msg = $this->Message->find('first', array('conditions' => $conditions, 'fields' => array('id')));
				if (! ($this->params['allow-dups'] || empty($existing_msg))) {
					$msgs_skipped++;
					$this->out(__("   Skipping (message already exists in database with id=%s)", $existing_msg['Message']['id']), 1, Shell::VERBOSE);
				} else {
					$this->Message->create();
					$this->Message->set('from_address', $msg['min']);
					$this->Message->set('message', $msg['msg']);
					$this->Message->set('source_id', $ms['id']);
					$this->Message->set('is_outbound', 0);
					$this->Message->set('status', Status::$STATUS_AVAILABLE);
					if ($this->Message->save()) {
						$msgs_saved++;
						$this->out(__("   Saved OK"), 1, Shell::VERBOSE);
					} else {
						$msgs_failed++;
						$this->out(__("   Saved FAILED"), 1, Shell::NORMAL);
					}
				}
			}
			$this->out(__("Incoming messages received: %s, saved: %s, skipped: %s, failed: %s", 
				$msgs_received, $msgs_saved, $msgs_skipped, $msgs_failed), 1, Shell::NORMAL);
			$this->out(__("Done"), 1, Shell::VERBOSE);
		} else {
			$ret_val = MessageSource::decode_netcast_retval($ret_val);
			$this->error("GETINCOMING fail", __("Gateway did not respond with a list: %s", $ret_val));
		}
	}
	
	/*
	 * SENDSMS Description: Send an SMS 
	 * message Parameters: 
	 *    Destination mobile no., your message, and your Netcast ID 
	 *    Optional: Custom sender mask
	 */
	public function send_sms() {
		$source = $this->get_message_source($this->args[0]);
		$ms = $source['MessageSource'];
		$this->out(__("Sending outgoing messages to message source \"%s\"", $ms['name']), 1, Shell::VERBOSE);
		$this->check_url($ms);
		$conditions = array(
			'Message.status' => Status::$STATUS_PENDING,
			'Message.is_outbound' => 1,
			array("NOT" => array(
			        "Message.to_address" => null
			    )
			)
		);
		$out_msgs = $this->Message->find('all', array('conditions' => $conditions));
		$msgs_queued = count($out_msgs);
		$msgs_sent = 0;
		$msgs_failed = 0;
		$msgs_sent_unsaved = 0;
		$last_err_msg = "";
		$this->out(__("Messages queued to be sent: %s", $msgs_queued), 1, Shell::VERBOSE);
		if (!empty($out_msgs)) {
			$netcast_mask = "FixMyBgy";
			// $netcast = $this->get_netcast_connection($ms);
			foreach ($out_msgs as $msg) {
				$this->out(__("    Sending message id=%s", $msg['Message']['id']), 1, Shell::VERBOSE);
				$this->out(__("                    to=%s", $msg['Message']['to_address']), 1, Shell::VERBOSE);
				// $ret_val = $this->call_netcast_function($netcast, "SENDSMS", array(
				//    $msg['Message']['to_address'], $msg['Message']['message'], $ms['remote_id'], $netcast_mask
				// ));
				// []--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]
				$ret_val = rand(1000, 9999); // FIXME faking a return code for now
				// []--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]--[]
				if (preg_match('/^\d+$/', $ret_val)) { // sent OK 'cos we got a numeric transaction id back
					$transaction_id = $ret_val;
					$this->Message->id = $msg['Message']['id'];
					$this->Message->set('status', Status::$STATUS_SENT);
					$this->Message->set('external_id', $transaction_id);
					if ($this->Message->save()) {
						$msgs_sent++;
						$this->out(__("   Sent and updated OK"), 1, Shell::VERBOSE);
						$this->log_action($msg['Message']['id'], __("sent to gateway %s", $ms['name']), $transaction_id);
					} else {
						$msgs_sent_unsaved++;
						$this->out(__("   Sent but update failed"), 1, Shell::NORMAL);
						$this->log_action($msg['Message']['id'], __("sent to gateway %s but status update failed", $ms['name']), $transaction_id);
					}
				} else {
					$msgs_failed++;
					$ret_val = MessageSource::decode_netcast_retval($ret_val);
					$last_err_msg = __("Gateway did not return a transaction id: %s", $ret_val); 
					$this->out($last_err_msg, 1, Shell::NORMAL);
					// later: add fail count to prevent endless repeats
				}
			}
		} 
		$this->out(__("Outgoing messages in queue: %s, sent: %s, failed: %s, sent-but-not-updated: %s", 
			$msgs_queued, $msgs_sent, $msgs_failed, $msgs_sent_unsaved), 1, Shell::NORMAL);
		if ($msgs_failed > 0) {
			$this->error("GETINCOMING fail", __("Messages failed: %s, last message was: %s", $msgs_failed, $last_err_msg));
		}
		$this->out(__("Done"), 1, Shell::VERBOSE);
	}
	

	private function get_message_source($id_or_name) {
		$source = null;
		if (preg_match('/^\d+$/', $id_or_name)) {
		 	$source = $this->MessageSource->findById($id_or_name);
		} else {
			$source = $this->MessageSource->findByName($id_or_name);
		}
		if (empty($source)) {
			$this->error("No such source", __("Could not find a message source that matched the id (or name) that you provided"));
		}
		return $source;
	}
	
	private function get_netcast_connection($ms) {
		require_once("nusoap/nusoap.php");
		return new SoapClient($ms['url']);
	}
	
	private function call_netcast_function($conn, $function_name, $param_array) {
		return $conn->__soapCall($function_name, $param_array); 
	}
	
	private function check_url($message_source) {
		$url = $message_source['url'];
		if (empty($url)) {
			$this->error("Missing URL", 'No test was run: you need to add a URL to the Message Source');
		} elseif (! preg_match('/^https?:\/\//', $url)) {
			$this->error("Missing protocol", 'No test was run: URL must start with protocol (http or https');
		}		
	}
	
	private function log_action($id, $note, $transaction_id=null) {
		$action = new Action;
		$params = array(
			'type_id' =>  ActionType::$ACTION_GATEWAY,
			'message_id' => $this->Message->id,
			'note' => $note,
			'item_id' => $transaction_id
		);
		$action->create($params);
		$action->save();
	}
	
}

