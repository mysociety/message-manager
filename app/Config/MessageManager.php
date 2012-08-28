<?php
$config = array(

	/*-----------------------------------------------
	 * tags:
	 * if messages come in with these prefixes, 
	 * store it as the message tag.
	 *-----------------------------------------------*/
    'tags' => array(
        'LUZ' => "Barangay Luz",
        'BSN' => "Barangay Basak San Nicolas"
    ),


	/*-----------------------------------------------
	 * If a tag is found, should it be removed from 
	 * the message?
	 *   0 = no
	 *   1 = yes
	 *-----------------------------------------------*/
	'remove_tags_when_matched' => 1,

	
	/*-----------------------------------------------
	 * URL of the FixMyStreet-like site (where the
	 * messages are assigned to problem reports):
	 *
	 * fms_site_url is the site URL without trailing slash.
	 * fms_report_path is path on that site to individual
	 *     reports, where %s will be replaced with the 
	 *     message's fms_id.
	 *-----------------------------------------------*/
	'fms_site_url'    => 'http://www.fixmystreet.com',
	'fms_report_path' => '/report/%s',

	/*-----------------------------------------------
	 * Allow the dummy client to run?
	 * Useful for setting up: but disable this in a
	 * production environment.
	 * 0 = disable
	 * 1 = enable
	 *-----------------------------------------------*/
	'enable_dummy_client' => 0,

	/*-----------------------------------------------
	 * Number of seconds that a lock is held
	 *-----------------------------------------------*/
    'lock_expiry_seconds' => 60 * 6,

	/*-----------------------------------------------
	 * mySociety's own deployment mechanism uses a 
	 * general.yml file to populate the database config 
	 * (amongst other things). If you /know/ your deployment 
	 * won't, you can tell Cake not to bother looking for it:
	 * 0 = don't try to read general.yml
	 * 1 = look for it, and use it if it's there! 
	 *-----------------------------------------------*/
	'might_use_general_yml' => 1,

	/*-----------------------------------------------
	 * Comma seperated list of sites that can access the
	 * JSON API
	 *-----------------------------------------------*/
	'cors_allowed' => 'http://www.fixmystreet.com',
	
	/*-----------------------------------------------
	 * Period within which a message can be automatically
	 * considered as a possible reply to a sent message
	 *-----------------------------------------------*/
	'autodetect_reply_period' => '1 week'
	
);

// The following quirky bit o' code lets Cake pull the database config from the
// general.yml file (if it exists): this is useful for mySociety because it fits
// in with our automatic deployment mechanism, but is safe for others to skip. In
// fact, you can spare Cake the trouble of trying to read the file if you know it
// won't be there by setting might_use_general_yml to 0 (see
// app/Config/MessageManager.php).

if ($config['might_use_general_yml']==1) {
	// ** This is much nicer...
	//     App::uses('Spyc', 'Lib');
	// ** but on (fastcgi?) deployment, it won't play nicely, so instead do: 
	$got_spyc = include(APP . 'Lib' . DS . 'spyc.php' );
	$general_config = Spyc::YAMLLoad(APP . 'Config/general.yml'); 
	if ( is_array($general_config) ) { 
		foreach ( $general_config as $full_name=>$data ) {
			$name = strtolower(str_replace('MESSAGE_MANAGER_', '', $full_name));
			if (is_array($data)) {
				$new_array = array();
				foreach($data as $k => $v) {
					if (is_array($v)) {
						foreach($v as $k1 => $v1) {
							$new_array[$k1]=$v1;
						}
					} else {
						$new_array = $v; // unexpected
					}
				}
				$config[$name]=$new_array;
			} else {
				$config[$name]=$data;
			}
		}
	}
} 
