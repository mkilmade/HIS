<?php
/**
 *
 * @author Mike Kilmade
 *
 */
class Trainer extends \Resource {
	public static $table = "trainer";
	public static $id_fld = "name";

	public function getIndividualMeetStats(string $meet_filter) {
		return TB17::getIndividualMeetStats ( "trainer", $this->name, $meet_filter );
	}
}