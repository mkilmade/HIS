<?php
require_once ('includes/envInit.inc.php');

// called by getTrend.php
function classTally(array $defaults) {
	$rm = Meet::IdFactory( $defaults ['race_meet_id'] );
	$tallies = $rm->getClassTally ();
	
	echo "
	  <div id='classTrendDetailDiv'></div>
      <div id='classTrendDiv'>
      <table id='classTable' 
             class='tablesorter'
             style='width:600px;font-size:14px;'>
        <caption>Class Breakdown for Meet (only if class has 5 or more races; total include all classes)</caption>
        <thead>
          <th>Class</th>
          <th>Races</th>
          <th>Favs</th>
          <th>Pct Favs</th>
          <th>Avg Odds</th>
          <th>Std Dev</th>
          <th>CoV</th>
        </thead>
    <tbody>
    ";

	buildTallyHtml($tallies, true);
	
    echo "</tbody></table>
        <script>
            $('#classTable').tablesorter({
                widgets: ['zebra']
            });
            $('#classTrendDiv').css({
                'float': 'left',
                'margin-right': '',
                'margin-left': '',
                'width': ''
              });
        </script>
        ";
    
    echo "
      <table id='dayTable' 
             class='tablesorter'
             style='width:600px; margin: auto; font-size:14px;'>
        <caption>Day of Week Breakdown for Meet</caption>
        <thead>
          <th>Class</th>
          <th>Races</th>
          <th>Favs</th>
          <th>Pct Favs</th>
          <th>Avg Odds</th>
          <th>Std Dev</th>
          <th>CoV</th>
        </thead>
    <tbody>
    ";
    $tallies = $rm->getDayTally ();
    buildTallyHtml($tallies, false);
    echo "</tbody></table></div>
        <script>
            $('#dayTable').tablesorter({
                widgets: ['zebra']
            });
        </script>
        ";
    
    
    
} // function

function buildTallyHtml(array $tallies, bool $click) {
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
        <tr><td>Total</td><td>$favTotal</td><td>$total</td><td>$pctFavs %</td><td colspan='3'>includes all horses in deadheats</td></tr>";
	
}

function calcCOV(float $avg, float $stddev) {
	$cov = [];
	$cov['stat'] = round($stddev/$avg,1);
	$cov['style'] = $cov['stat'] >= 1.3 ? "style='background: LightPink'" : "";
	return $cov;
}

?> 