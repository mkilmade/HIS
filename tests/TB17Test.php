<?php
use PHPUnit\Framework\TestCase;
/**
 * test case.
 */
class TB17Test extends TestCase {

	public function testGetRaceInfo() {

		$obj = TB17::getRaceInfo ( '2018-08-08', '9', 'SAR' );
		$this->assertSame ( 'World Of Trouble', $obj->horse, $obj->horse );

	}

	public function testLastRaceDateCheck() {

		$lrd = TB17::last_race_date ();
		$this->assertSame ( '2018-12-19', $lrd, $lrd );

	}
}

