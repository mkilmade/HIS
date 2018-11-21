<?php
/**
 *
 * @author Mike Kilmade
 *
 */
require_once('Connection.class.php');
spl_autoload_register(function ($class) {
	require_once $class . '.class.php';
});
	
	class Trainer extends \HisEntity {
	public function __construct($id, $conn = NULL) {
		$this->bindings['table']   = "trainer";
		$this->bindings['key_fld'] = "name";
		$this->bindings['type']    = "s";
		parent::__construct ( $id, $conn = NULL );
	}
	
	public function getIndividualMeetStats($meet_filter) {
		return TB17::getIndividualMeetStats("trainer", $this->name, $meet_filter);
	}
}

