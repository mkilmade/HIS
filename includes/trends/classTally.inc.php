<?php
spl_autoload_register(function ($class) {
	require_once 'classes/' . $class . '.class.php';
});
// called by getTrend.php
	function classTally($defaults)
{
	$rm = new Meet($defaults['race_meet_id']);
	$tallies = $rm->getClassTally();
	
    echo "
      <table id='classTable' class='tablesorter' style='width:200px; margin: auto; font-size:14px'>
        <caption>Class Breakdown for Meet (" . count($tallies) . ")</caption>
        <thead>
          <th>Class</th>
          <th>Races</th>
        </thead>
    <tbody>
    ";

    $total = 0;
    foreach($tallies as $tally) {
    	$total += $tally['races'];
        echo "<tr>";
        echo "<td style='text-align:left;'>{$tally['race_class']}</td>";
        echo "<td>{$tally['races']}</td>";
        echo "</tr>";
    }

    echo "
        <tr><td>Total</td><td>$total</td></tr>
        </tbody></table>

        <script>
            $('#classTable').tablesorter({
                widgets: ['zebra']
            });
        </script>
        ";

} // function

?> 