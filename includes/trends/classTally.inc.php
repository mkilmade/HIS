<?php
require_once ('includes/envInit.inc.php');

// called by getTrend.php
function classTally($defaults) {
	$rm = Meet::IdFactory( $defaults ['race_meet_id'] );
	$tallies = $rm->getClassTally ();
	$dayTallies = $rm->getDayTally ();
	
	echo "
      <table id='classTable' class='tablesorter' style='width:600px; margin: auto; font-size:14px'>
        <caption>Class Breakdown for Meet (" . count ( $tallies ) . ")</caption>
        <thead>
          <th>Class</th>
          <th>Races</th>
          <th>Favs</th>
          <th>Pct Favs</th>
          <th>Avg Odds</th>
          <th>Std Dev</th>
          <th>CV</th>
        </thead>
    <tbody>
    ";

	$total = 0;
	$favTotal = 0;
	foreach ( $tallies as $tally ) {
		$total += $tally ['races'];
		$favTotal += $tally ['favs'];
		$pctFavs = round(($tally ['favs']/$tally ['races'])*100,1);
		$cv = calcCV($tally['avg_odds'], $tally['std_dev']);
		echo "<tr>";
		echo "<td style='text-align:left;'>{$tally['race_class']}</td>";
		echo "<td>{$tally['races']}</td>";
		echo "<td>{$tally['favs']}</td>";
		echo "<td>$pctFavs %</td>";
		echo "<td>{$tally['avg_odds']}</td>";
		echo "<td>{$tally['std_dev']}</td>";
		echo "<td {$cv['style']} >{$cv['stat']}</td>";
		echo "</tr>";
	}
	$pctFavs = round(($total/$favTotal)*100,1);
	echo "
        <tr><td>Totals</td><td>$favTotal</td><td>$total</td><td>$pctFavs %</td><td colspan='3'>includes both horses in deadheats</td></tr>
        <tr><td colspan='7'><b>Day Stats</b></td></tr>";
    
	$total=0;
	$favTotal = 0;
	foreach ( $dayTallies as $tally ) {
        $total += $tally ['races'];
        $favTotal += $tally ['favs'];
        $pctFavs = round(($tally ['favs']/$tally ['races'])*100,1);
        $cv = calcCV($tally['avg_odds'], $tally['std_dev']);
		echo "<tr>";
		echo "<td style='text-align:left;'>{$tally['day']}</td>";
		echo "<td>{$tally['races']}</td>";
		echo "<td>{$tally['favs']}</td>";
		echo "<td>$pctFavs %</td>";
		echo "<td>{$tally['avg_odds']}</td>";
		echo "<td>{$tally['std_dev']}</td>";
		echo "<td {$cv['style']} >{$cv['stat']}</td>";
		echo "</tr>";
	}
	$pctFavs = round(($favTotal/$total)*100,1);
    echo "
        <tr><td>Total</td><td>$favTotal</td><td>$total</td><td>$pctFavs %</td><td colspan='3'>includes both horses in deadheats</td></tr>
              </tbody></table>

        <script>
            $('#classTable').tablesorter({
                widgets: ['zebra']
            });
        </script>
        ";
} // function

function calcCV($avg, $stddev) {
	$cv = [];
	$cv['stat'] = round($stddev/$avg,1);
	$cv['style'] = $cv['stat'] >= 1.3 ? "style='background: LightPink'" : "";
	return $cv;
}

?> 