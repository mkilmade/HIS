<?php
require_once ('includes/envInit.inc.php'); // called by getTrend.php
function previousTrackWins($defaults) {
	$rm = Meet::IdFactory( $defaults ['race_meet_id'] );
	$tallies = $rm->getPreviousTrackWins ();

	echo "
      <table id='trackTable' class='tablesorter' style='width:200px; margin: auto; font-size:14px'>
        <caption>Previous Track Win Stats (" . count ( $tallies ) . " Tracks)</caption>
        <thead><tr>
          <th>Track</th>
          <th>Dirt</th>
          <th>Turf</th>
          <th>Total</th>
        </tr></thead>
    <tbody>
    ";

	$dirt = 0;
	$turf = 0;
	$wins = 0;
	foreach ( $tallies as $tally ) {
		$dirt  += $tally ['dirt'];
		$turf  += $tally ['turf'];
		$wins += $tally ['wins'];
		echo "<tr>";
		echo "<td>{$tally['previous_track_id']}</td>";
		echo "<td>{$tally['dirt']}</td>";
		echo "<td>{$tally['turf']}</td>";
		echo "<td>{$tally['wins']}</td>";
		echo "</tr>";
	}

	// get FTS (first time starter) wins for meet
	$totals = $rm->getFtsWins ();
	$dirt += $totals['dirt'];
	$turf += $totals['turf'];
	$wins += $totals['wins'];
	echo "<tr>";
	echo "<td>Firsters</td>";
	echo "<td>{$totals['dirt']}</td>";
	echo "<td>{$totals['turf']}</td>";
	echo "<td>{$totals['wins']}</td>";
	echo "</tr>";

	// add total row
	echo "
        <tr>
            <td><b>Total</b></td>
            <td><b>$dirt</b></td>
            <td><b>$turf</b></td>
            <td><b>$wins</b></td>
        </tr>
        </tbody></table>

        <script>
            $('#trackTable').tablesorter({
              widgets: ['zebra']
            });
         </script>
        ";
} // function

?>