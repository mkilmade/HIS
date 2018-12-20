<?php
require_once ('includes/envInit.inc.php'); // called by getTrend.php
function previousRaceAtMeetPerCard($defaults) {
	$rm = new Meet ( $defaults ['race_meet_id'] );
	$tallies = $rm->getPreviousRaceAtMeetPerCard ();

	echo "
      <table id='previousMeetDateCountTable' class='tablesorter' style='width:200px; margin: auto; font-size:14px'>
        <caption>Winner's Previous Race At Meet</caption>
        <thead>
          <th>Date</th>
          <th>Wins per card</th>
        </thead>
    <tbody>
    ";

	foreach ( $tallies as $date_tally ) {
		if ($date_tally ['wins'] == 0) {
			continue;
		}
		$dt = substr ( $date_tally ['race_date'], 5, 5 );
		echo "<tr>";
		echo "<td>$dt</td>";
		echo "<td>{$date_tally['wins']} of {$date_tally['races']}</td>";
		echo "</tr>";
	}

	echo "
        </tbody></table>
        <script>
            $('#previousMeetDateCountTable').tablesorter({
                widgets: ['zebra'],
                headers: {
                  1: {
                    sorter: false
                  }
                }
           });
        </script>
        ";
} // function