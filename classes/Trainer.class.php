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
	
	class Trainer extends \Resource {
		public function __construct($id = NULL, HIS\Connection $conn = NULL) {
		$this->bindings['table']   = "trainer";
		$this->bindings['key_fld'] = "name";
		$this->bindings['type']    = "s";
		parent::__construct ( $id, $conn = NULL );
	}
	
	public function getIndividualMeetStats(string $meet_filter) {
		return TB17::getIndividualMeetStats("trainer", $this->name, $meet_filter);
	}
}

