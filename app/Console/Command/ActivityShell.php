<?php

/*-----------------------------------------------------------------
 * Activity shell commands:
 *---------------------------------------------------------------------
 * These subcommands are available:
 *
 * activity dump:    dump activity records out like a log file (maybe
 *                   allow purging too, since most of the activity
 *                   data probably neededn't stay in the database)
 *---------------------------------------------------------------------
 *
 * Issue with: (e.g.) "cd app & Console/cake activity dump"
 * Note that Cake's shells output helpful usage info!
 *
 */

class ActivityShell extends AppShell {
	public $uses = array('Action', 'ActionType', 'User');

	/* suppress output/header message, and colour */
	public function startup() {
		if ($this->params['plain']) {
			$this->stdout->outputAs(ConsoleOutput::PLAIN); // NB wanted to use ->output-> as in the docs
			$this->stderr->outputAs(ConsoleOutput::PLAIN); //    but alas, it didn't work
		}
		$this->out("Running activity shell for Message Manager", 2, Shell::VERBOSE);
	}

	public function getOptionParser() {
	    $parser = parent::getOptionParser();

		$parser->addSubcommand('dump', array(
			'help' => __('dump activity records'),
			'parser' => array(
				'options' => array(
					'plain' => array(
						'short' => 'p',
						'help' => __('Plain output (suppresses colour), good for cron jobs.'),
						'boolean' => true,
						'default' => false
					),
					'type' => array(
						'short' => 't',
						'help' => __('only dump specified action types (space-separated), defaults to all types.'),
						'boolean' => false,
						'default' => ""
					),
					'exclude-type' => array(
						'short' => 'T',
						'help' => __('exclude specified action types (space-separated) ' .
										'This option is ignored if you specify an include-types option.'),
						'boolean' => false,
						'default' => ""
					),
					'after-date' => array(
						'short' => 'a',
						'help' => __('dump activity that occurred after this date (YYYY-MM-DD)'),
						'boolean' => false,
						'default' => ""
					),
					'before-date' => array(
						'short' => 'b',
						'help' => __('dump activity that occurred before this date (YYYY-MM-DD)'),
						'boolean' => false,
						'default' => ""
					),
				)
			)
		));
	    return $parser;
	}

	public function dump() {
		$conditions = array();
		$action_types_excluded = array();
		if ($this->params['type']) {
			$raw_types_included = preg_split("/(\s|,)\s*/", strtolower($this->params['type']));
			$conditions["ActionType.name"] = $raw_types_included;
			$this->out(__("  Including types: %s", implode(" ", $raw_types_included)), 1, Shell::VERBOSE);
			if ($this->params['exclude-type']) {
				$this->out(__("  The exclude-type option (value: \"%s\") is being ignored, because you specified a -t option", $this->params['exclude-types']), 1, Shell::VERBOSE);
			}
		} else {
			if ($this->params['exclude-type']) {
				$raw_types_excluded = preg_split("/(\s|,)\s*/", strtolower($this->params['exclude-type']));
				$conditions["NOT"] = array("ActionType.name" => $raw_types_excluded);
				$this->out(__("  Excluding types: %s", implode(" ", $raw_types_excluded)), 1, Shell::VERBOSE);
			} else {
				$this->out(__("  Excluding no types"), 1, Shell::VERBOSE);
			}
		}
		if ($this->params['after-date']) {
			$conditions["Action.created >"] = $this->params['after-date'];
			$this->out(__("  Created after: %s", $this->params['after-date']), 2, Shell::VERBOSE);
		}
		if ($this->params['before-date']) {
			$conditions["Action.created <"] = $this->params['before-date'];
			$this->out(__("  Created before: %s", $this->params['before-date']), 2, Shell::VERBOSE);
		}
		
		$actions = $this->Action->find('all', array(
			'conditions' => $conditions,
			'order' => 'Action.created'
		));
		// id, created, type_id, user_id, message_id, item_id, note

		$this->out(sprintf("%-19s  %-8s  %-12s  %s", "date", "type", "user", "  msg  item:note"), 1, Shell::VERBOSE);
		$this->out(sprintf("%-19s  %-8s  %-12s  %s", "----", "----", "----", "  --------------"), 1, Shell::VERBOSE);
		foreach ($actions as $a) {
			$this->out(sprintf("%19s  %-8s  %-12s  %5s  %s: %s",
				$a['Action']['created'],
				$a['ActionType']['name'],
				$a['User']['username'],
				$a['Action']['message_id'],
				$a['Action']['item_id'],
				$a['Action']['note'] /* take out newlines */
			), 1, Shell::NORMAL);
		}
		$this->out(__("Done"), 1, Shell::VERBOSE);
	}

}

