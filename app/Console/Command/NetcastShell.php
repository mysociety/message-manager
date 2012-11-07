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
	public $uses = array('MessageSource');

	public function gateway_test() {
		$source = $this->get_message_source($this->args[0]);
		$ms = $source['MessageSource'];
		$this->out("Message source is: " . $ms['name']);
		$netcast_id = $ms['remote_id'];
		$url = $ms['url'];
		if (empty($url)) {
			$connection_test_result = 'No test was run: you need to specify a URL';
		} elseif (! preg_match('/^https?:\/\//', $url)) {
			$connection_test_result = 'No test was run: URL must start with protocol (http or https)';
		} else {
			require_once("nusoap/nusoap.php");
			$netcast = $this->get_netcast_connection($ms);
			$ret_val = $this->call_netcast_function($netcast, $ms, "GETCONNECT");
			$ret_val = MessageSource::decode_netcast_retval($ret_val);
			$this->out($ret_val);
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
			exit("Could not find a message source that matched the id (or name) that you provided\n");
		}
		return $source;
	}
	
	private function get_netcast_connection($ms) {
		return new SoapClient($ms['url']);
	}
	
	private function call_netcast_function($conn, $ms, $function_name) {
		return $conn->__soapCall($function_name, array($ms['remote_id'])); 
	}
}

