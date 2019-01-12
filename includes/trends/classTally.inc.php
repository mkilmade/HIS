<?php
require_once ('includes/envInit.inc.php');

// called by getTrend.php
function classTally($defaults) {
	$rm = Meet::IdFactory( $defaults ['race_meet_id'] );
	$tallies = $rm->getClassTally ();

	echo "
      <table id='classTable' class='tablesorter' style='width:300px; margin: auto; font-size:14px'>
        <caption>Class Breakdown for Meet (" . count ( $tallies ) . ")</caption>
        <thead>
          <th>Class</th>
          <th>Races</th>
          <th>Avg Odds</th>
        </thead>
    <tbody>
    ";

	$total = 0;
	foreach ( $tallies as $tally ) {
		$total += $tally ['races'];
		echo "<tr>";
		echo "<td style='text-align:left;'>{$tally['race_class']}</td>";
		echo "<td>{$tally['races']}</td>";
		echo "<td>{$tally['avg_odds']}</td>";
		echo "</tr>";
	}

	echo "
        <tr><td>Total</td><td>$total</td><td/></tr>
        </tbody></table>

        <script>
            $('#classTable').tablesorter({
                widgets: ['zebra']
            });
        </script>
        ";
} // function

?> 