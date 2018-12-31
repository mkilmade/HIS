<?php
/**
 *
 * @author Mike Kilmade
 *
 */
class Jockey extends \Resource {
	const TABLE = "jockey";
	const ID_FLD = "name";
	public function getIndividualMeetStats(string $meet_filter) {
		return TB17::getIndividualMeetStats ( "jockey", $this->name, $meet_filter );
	}
}

