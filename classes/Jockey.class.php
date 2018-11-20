<?php
/**
 *
 * @author Mike Kilmade
 *
 */
spl_autoload_register(function ($class) {
	//echo "...including " . $class;
	include $class . '.class.php';
});

	class Jockey extends \HisEntity {
	public function __construct($id, $conn = NULL) {
		$this->bindings['table']   = "jockey";
		$this->bindings['key_fld'] = "name";
		$this->bindings['type']    = "s";
		parent::__construct ( $id, $conn = NULL );
	}
	
	public function getIndividualMeetStats($meet_filter) {
		return TB17::getIndividualMeetStats("jockey", $this->name, $meet_filter);	
	}
}

