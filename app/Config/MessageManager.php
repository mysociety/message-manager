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
	 * Useful for setting up: but disbale this in a
	 * production environment.
	 * 0 = disable
	 * 1 = enable
	 *-----------------------------------------------*/
	'enable_dummy_client' => 1,

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
);