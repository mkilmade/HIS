<?php
/**
 *
 * @author Mike Kilmade
 *
 */
class Trainer extends \Resource {
	const TABLE  = "trainer";
	const ID_FLD =  "name";

	public function getIndividualMeetStats(string $meet_filter) {
		return TB17::getIndividualMeetStats ( "trainer", $this->name, $meet_filter );
	}
}