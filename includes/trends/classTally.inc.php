<?php
require_once ('includes/envInit.inc.php');

// called by getTrend.php
function classTally(array $defaults) {
	$rm = Meet::IdFactory( $defaults ['race_meet_id'] );
	$tallies = $rm->getClassTally ();
    echo "
	  <div id='classTrendDetailDiv'></div>
      <table id='classTable'
             class='tablesorter'
             style='width:600px; font-size:14px; float: left'>
        <caption>Class Breakdown for Meet (only if class has 5 or more races; total include all classes)</caption>
    ";	
	buildTallyHtml($tallies, true);
	echo "</table>

      <div style='width: 650px; float: left'>
      <table id='dayTable' 
             class='tablesorter'
             style='width:625px; font-size:14px;' >
        <caption>Day of Week Breakdown for Meet</caption>
    ";
    
    $tallies = $rm->getDayTally ();
    buildTallyHtml($tallies, false);
    echo "</table></div>

        <script>
            $('#dayTable, #classTable').tablesorter({
                widgets: ['zebra']
            });
           $('#classTrendDetailDiv').css({ 'visibility': 'hidden',
                                           'float': 'left',
                                           'width': '225px'
                                         });

        </script>
        ";
} // function

function buildTallyHtml(array $tallies, bool $click) {
	echo "
        <thead>
          <th>" . ($click ? 'Class' : 'DoW') . "</th>
          <th>Races</th>
          <th>Favs</th>
          <th>Pct Favs</th>
          <th>Avg Odds</th>
          <th>Std Dev</th>
          <th>CoV</th>
        </thead>
    <tbody>
    ";
	$total=0;
	$favTotal = 0;
	foreach ( $tallies as $tally ) {
		$total    += $tally ['races'];
		$favTotal += $tally ['favs'];
		if ($tally['races'] < 6) {
			continue;
		}
		$pctFavs = round(($tally ['favs']/$tally ['races'])*100,1);
		
		// Coefficient Of Variation
		$cov = calcCOV($tally['avg_odds'], $tally['std_dev']);
		
	    $click =  ($click ? "onclick=\"getClassTrendDetail('classTally','{$tally['item']}')\"" : "");
		echo "<tr $click>";
		echo "<td style='text-align:left;'>{$tally['item']}</td>";
		echo "<td>{$tally['races']}</td>";
		echo "<td>{$tally['favs']}</td>";
		echo "<td>$pctFavs %</td>";
		echo "<td>{$tally['avg_odds']}</td>";
		echo "<td>{$tally['std_dev']}</td>";
		echo "<td {$cov['style']} >{$cov['stat']}</td>";
		echo "</tr>";
	}
	$pctFavs = round(($favTotal/$total)*100,1);
	echo "
        <tr><td>Total</td>
            <td>$favTotal</td>
            <td>$total</td><td>$pctFavs %</td>
            <td colspan='3'>includes all horses in deadheats</td>
        </tr>
        </tbody>";
	
}

function calcCOV(float $avg, float $stddev) {
	$cov = [];
	$cov['stat'] = round($stddev/$avg,1);
	$cov['style'] = $cov['stat'] >= 1.3 ? "style='background: LightPink'" : "";
	return $cov;
}

?> 