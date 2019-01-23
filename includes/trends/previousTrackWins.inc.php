<?php
require_once ('includes/envInit.inc.php'); // called by getTrend.php
function previousTrackWins($defaults) {
	$rm = Meet::IdFactory( $defaults ['race_meet_id'] );
	$tallies = $rm->getPreviousTrackWins ();

	echo "
      <table id='trackTable' class='tablesorter' style='width:200px; margin: auto; font-size:14px'>
        <caption>Previous Track Stats (" . count ( $tallies ) . ")</caption>
        <thead>
          <th>Track</th>
          <th>Wins</th>
        </thead>
    <tbody>
    ";

	$total = 0;
	foreach ( $tallies as $tally ) {
		$total += $tally ['wins'];
		echo "<tr>";
		echo "<td>{$tally['previous_track_id']}</td>";
		echo "<td>{$tally['wins']}</td>";
		echo "</tr>";
	}

	// get FTS (first time starter) wins for meet
	$wins = $rm->getFtsWins ();
	$total += $wins;
	echo "<tr>";
	echo "<td>Firsters</td>";
	echo "<td>$wins</td>";
	echo "</tr>";

	// add total row
	echo "
        <tr>
            <td><b>Total</b></td>
            <td><b>$total</b></td>
        </tr>
        </tbody></table>

        <script>
            $('#trackTable').tablesorter({
              widgets: ['zebra']
            });
            setTrendDivStyle('center');
        </script>
        ";
} // function

?>