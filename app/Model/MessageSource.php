<?php
class MessageSource extends AppModel {
	public $name = 'MessageSource';
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => array('User.group_id' => '4'), // FIXME 'message-sources' hardcoded!
			'fields' => array('username'),
			'order' => array('User.username' => 'ASC')
		)
	);
	public $actsAs = array('PaginatesOnPostgres');
	
	// for the Netcast SMS gateway, these are the preconfigured return codes
	public static function decode_netcast_retval($code) {
		switch ($code) {
			case 'RETEMP01':
				$s="Netcast ID is empty";
				break;
			case 'RETEMP02':
				$s="Mobile Number is empty";
				break;
			case 'RETEMP03':
				$s="Message is empty";
				break;
			case 'RETEMP05':
				$s="Transaction Reference Number is empty";
				break;
			case 'RETEMP06':
				$s="Date is empty";
				break;
			case 'RETGMS01':
				$s="Pending/Queued";
				break;
			case 'RETGMS02':
				$s="SMS Sent";
				break;
			case 'RETGMS03':
				$s="SMS Sending Failed";
				break;
			case 'RETGMS04':
				$s="Invalid Transaction Reference Number";
				break;
			case 'RETVAL01':
				$s="Unauthorized IP address";
				break;
			case 'RETVAL02':
				$s="Unauthorized Netcast ID";
				break;
			case 'RETVAL03':
				$s="Invalid Mobile Number";
				break;
			case 'RETVAL04':
				$s="Unrecognized Mobile Number";
				break;
			case 'RETVAL05':
				$s="Message contains illegal characters";
				break;
			case 'RETVAL07':
				$s="Unauthorized Custom Mask";
				break;
			default:
				if (preg_match('/^Welcome/', $code)) {
					$s = "connected OK";
				} else {
					$s="Unknown return code";
				}
		}
		return "$code: $s";
	}
}
