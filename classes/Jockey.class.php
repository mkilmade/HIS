<?php
/**
 *
 * @author Mike Kilmade
 *
 */
	class Jockey extends \Resource {
		public function __construct($id = NULL, Connection $conn = NULL) {
		$this->bindings['table']   = "jockey";
		$this->bindings['key_fld'] = "name";
		$this->bindings['type']    = "s";
		parent::__construct ( $id, $conn );
	}
	
	public function getIndividualMeetStats($meet_filter) {
		return TB17::getIndividualMeetStats("jockey", $this->name, $meet_filter);	
	}
}

