<?php
class BoilerplateString extends AppModel {
	public $name = 'BoilerplateString';
	
	public $validate = array(
		// note MSISD is now also used for outgoing messages, e.g. username
		// In future may also be used for, e.g, twitter status: later, refactor by changing name?
		'lang' => array(
			'rule'    => 'notEmpty',
			'message' => 'Language must not be empty: for English strings, use "en"',
		),
		'text_value' => array(
			'rule'    => 'notEmpty',
			'message' => 'The boilerplate string must not be empty',
		),
		'type' => array(
			'rule'    => 'notEmpty',
			'message' => 'Type must not be empty: typical values are "reply" or "hide-reason"',
		),
		'sort_index' => array(
			'rule'    => 'numeric',
			'allowEmpty' => true,
			'message' => 'Sort index, which is used to force a sort order on your strings, must be numeric."',
		)
	);
	
	public $actsAs = array('PaginatesOnPostgres');
}
