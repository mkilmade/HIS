<?php
require_once ('includes/envInit.inc.php'); // called by getTrend.php
function multipleWins($defaults) {
	$rm = Meet::IdFactory( $defaults ['race_meet_id'] );
	$tallies = $rm->getMultipleWins ();

	echo "
          <table id='multiWinsTable' class='tablesorter' style='width:200px; margin: auto; font-size:14px'>
            <caption>Multiple Wins at Meet (" . count ( $tallies ) . ")</caption>
            <thead>
              <th>Horse</th>
              <th>Wins</th>
            </thead>
           <tbody>
        ";

	foreach ( $tallies as $horse ) {
		echo "<tr>";
		echo "<td style='text-align:left;'>{$horse['horse']}</td>";
		echo "<td>{$horse['wins']}</td>";
		echo "</tr>";
	}

	echo "
        </tbody></table>
        <script>
            $('#multiWinsTable').tablesorter({
              widgets: ['zebra']
            });
            $('#trendDiv').attr('style', \"<div id='trendDiv' style='margin-right: auto; margin-left: auto;width: 1000px; visibility:hidden;\");
        </script>
        ";
} // function

?>