<?php
require_once ('includes/envInit.inc.php'); // called by getTrend.php
function previousFinishTally($defaults) {
	$rm = new Meet ( $defaults ['race_meet_id'] );
	$tallies = $rm->getPreviousFinishTally ();

	echo "
      <table id='previousFinishTable' class='tablesorter' style='width:225px; margin: auto; font-size:14px'>
        <caption>Previous Race At Meet</caption>
        <thead>
          <th>Prev Finish</th>
          <th>Next Out Win</th>
        </thead>
    <tbody>
    ";
	$total = 0;
	foreach ( $tallies as $finishes ) {
		$total += $finishes ['count'];
		echo "<tr>";
		echo "<td>{$finishes['previous_finish_position']}</td>";
		echo "<td>{$finishes['count']}</td>";
		echo "</tr>";
	}
	echo "
        <tr><td>Total</td><td>$total</td></tr>
        </tbody></table>

        <script>
            $('#previousFinishTable').tablesorter({
                widgets: ['zebra'],
                headers: {
                    0: {
                        sorter: false
                    },
                    1: {
                        sorter: false
                    }
                }
          });
          ";
} // function

?>