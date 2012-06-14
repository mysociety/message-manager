<?php
App::uses('AppHelper', 'View/Helper');

class MessageUtilsHelper extends AppHelper {

	// return a string describing lock expiry (in 30 seconds, 2 days ago, etc)
	function pretty_lock_duration($seconds) {
		if ($seconds == 0) {
			return "expires now";
		} else {
			$s = abs($seconds);
			if ($s < 120) {
				$unit = "second";
			} else {
				$s = round($s/60);
				if ($s < 60) {
					$unit = "minute";
				} else {
					$s = round($s/60);
					if ($s < 24) {
						$unit = "hour";
					} else {
						$s = round($s/24);
						$unit = "day";
					}
				}
			}
			if ($s > 1) {
				$unit .= "s";
			}
			if ($seconds > 0) {
				return "expires in $s $unit";
			} else {
				return "expired $s $unit ago";
			}
		}
	}
	
	function fms_report_url($fms_id) {
		return Configure::read('fms_site_url') .
			preg_replace('/%s/', $fms_id, Configure::read('fms_report_path'));
	}
} 
