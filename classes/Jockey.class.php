<?php
/**
 *
 * @author Mike Kilmade
 *
 */
class Jockey extends \Resource {
	public static $table = "jockey";
	public static $id_fld = "name";

	public function getIndividualMeetStats(string $meet_filter) {
		return TB17::getIndividualMeetStats ( "jockey", $this->name, $meet_filter );
	}
}

