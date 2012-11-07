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
	public $uses = array('MessageSource', 'Message', 'Status');

	public function getOptionParser() {
	    $parser = parent::getOptionParser();
		$parser->addArgument('source_id', array(
					'help' => __('The id or name of the message souce (gateway)'), 'required' => true));
		$parser->addSubcommand('gateway_test', array(
					'help' => __('Test the connection to the gateway (like pinging).')));
		$parser->addSubcommand('get_incoming', array(
					'help' => __('Pull down incoming messages from the gateway and load them into the database.')));
		$parser->addOption('allow-dups', array(
					'help' => __('Save messages even if they already seem to be in the database (defaults to false, ' .
								'so duplicate messages will be skipped).'), 
					'boolean' => true,
					'short' => 'a',
					'default' => false));
	    return $parser;
	}
	
	public function gateway_test() {
		$source = $this->get_message_source($this->args[0]);
		$ms = $source['MessageSource'];
		$this->out(__("Testing connection to message source \"%s\"", $ms['name']), 1, Shell::VERBOSE);
		$this->check_url($ms);
		$netcast = $this->get_netcast_connection($ms);
		$ret_val = $this->call_netcast_function($netcast, $ms, "GETCONNECT");
		$ret_val = MessageSource::decode_netcast_retval($ret_val);
		$this->out($ret_val, 1, Shell::QUIET);
	}

	public function get_incoming() {
		$source = $this->get_message_source($this->args[0]);
		$ms = $source['MessageSource'];
		$this->out(__("Getting incoming messages from message source \"%s\"", $ms['name']), 1, Shell::VERBOSE);
		$this->check_url($ms);
		$netcast = $this->get_netcast_connection($ms);
		$ret_val = $this->call_netcast_function($netcast, $ms, "GETINCOMING");
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
	
	private function get_message_source($id_or_name) {
		$source = null;
		if (preg_match('/^\d+$/', $id_or_name)) {
		 	$source = $this->MessageSource->findById($id_or_name);
		} else {
			$source = $this->MessageSource->findByName($id_or_name);
		}
		if (empty($source)) {
			$this->error("No such source", "Could not find a message source that matched the id (or name) that you provided");
		}
		return $source;
	}
	
	private function get_netcast_connection($ms) {
		require_once("nusoap/nusoap.php");
		return new SoapClient($ms['url']);
	}
	
	private function call_netcast_function($conn, $ms, $function_name) {
		return $conn->__soapCall($function_name, array($ms['remote_id'])); 
	}
	
	private function check_url($message_source) {
		$url = $message_source['url'];
		if (empty($url)) {
			$this->error("Missing URL", 'No test was run: you need to add a URL to the Message Source');
		} elseif (! preg_match('/^https?:\/\//', $url)) {
			$this->error("Missing protocol", 'No test was run: URL must start with protocol (http or https');
		}		
	}
}

