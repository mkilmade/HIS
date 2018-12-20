<?php
require_once ('includes/envInit.inc.php'); // called by getTrend.php
function previouslyRanAtMeet($defaults) {
	$rm = new Meet ( $defaults ['race_meet_id'] );
	$tallies = $rm->getPreviouslyRanAtMeet ();

	echo "
      <table id='previouslyRanAtMeetWinTable' class='tablesorter' style='width:300px; margin: auto; font-size:14px'>
        <caption>Previous Race At Meet Before Win (" . count ( $tallies ) . ")</caption>
        <thead>
          <th>Horse</th>
          <th>Date</th>
        </thead>
    <tbody>
    ";

	foreach ( $tallies as $race ) {
		$dt = substr ( $race ['race_date'], 5, 5 );
		echo "<tr>";
		echo "<td>{$race['horse']}</td>";
		echo "<td>$dt</td>";
		echo "</tr>";
	}

	echo "</tbody></table>
        <script>
            $('#previouslyRanAtMeetWinTable').tablesorter({
              widgets: ['zebra']
            });
        </script>
    ";
} // function

?>