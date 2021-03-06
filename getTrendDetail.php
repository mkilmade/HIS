<?php
/*
 * @package Horse Information System (HIS)
 * @author Mike Kilmade <mkilmade.nycap.rr.com>
 * @version 0,001
 *
 * @name getTrend
 * @parmam string $_GET['trend'] requested trend name
 * @parmam string $_GET['param'] requesed item to detail
 * /
 *
 * /* set up common environment needed to process all trend code
 */
require_once ('includes/envInit.inc.php');
$trendName = $_GET ['trend'];
$param = $_GET ['param'];

$html = "";
if ($trendName == 'classTally') {
	$race_class = $param;
	
	$html = "<table id='classDetailTable' class='tablesorter' style='width:200px; margin: auto; font-size:14px'>";
	$html .= "<caption>Class: <b>$race_class</b></caption>";
	$html .= "<thead><th>Odds Range</th><th>Count / Favs</th></thead>";
	
	$rmObj = Meet::IdFactory ( $_SESSION ['defaults'] ['race_meet_id'] );
	$ranges = $rmObj->getClassTallyDetail ( $race_class );
	foreach ( $ranges as $range => $counts ) {
		$html .= "<tr><td>$range</td><td>{$counts['count']} / {$counts['favs']}</td></tr>";
	}
	$html .= "</tbody></table>
        <script>
            $('#classDetailTable').tablesorter({
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
        $('#classTrendDetailDiv').css({ 'visibility': 'visible'});
        </script>";
}
echo $html;
?>