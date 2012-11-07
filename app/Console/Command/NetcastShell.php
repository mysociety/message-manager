<?php

/*-----------------------------------------------------------------
 * Netcast shell commands:
 *
 * netcast gateway_test [source-name/id]
 * netcast getincoming [source-name/id]
 *
 * Issue with: "cd app & Console/cake netcast gateway_test 1"
 *
 *----------------------------------------------------------------*/
class NetcastShell extends AppShell {
	public $uses = array('MessageSource', 'Message', 'Status');

	public function getOptionParser() {
	    $parser = parent::getOptionParser();
		$parser->addArgument('source_id', array('help' => 'The id or name of the message souce (gateway)', 'required' => true));
		$parser->addSubcommand('gateway_test', array('help' => 'Test the connection to the gateway (like pinging).'));
		$parser->addSubcommand('get_incoming', array('help' => 'Pull down incoming messages from the gateway and load them into the database.'));
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
		if (is_array($ret_val)) {
			$this->out(__("Received incoming messages: %s", count($ret_val)), 1, Shell::VERBOSE);
			foreach ($ret_val as $msg) {
				$this->out(__("Processing message: [%s] %s", $msg['min'], $msg['msg']), 1, Shell::VERBOSE);
				// min => sender's number
				// msg => message text
			}
			$this->out(__("Done"), 1, Shell::VERBOSE);
		} else {
			$ret_val = MessageSource::decode_netcast_retval($ret_val);
			$this->out($ret_val, 1, Shell::QUIET);
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

