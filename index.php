<?php
if (isset ( $_GET ['reset_session'] ) && $_GET ['reset_session'] == 1) {
	session_start ();
	session_destroy ();
	// connect to index.php again w/o reset_session so it is remove from url
	// - if not called again, 'back' in browser resets again
	header ( 'Location: index.php' );
	return;
} else {
	require_once ('includes/envInit.inc.php');
}

?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link type="text/css" href="jquery/jquery-ui.min.css" rel="stylesheet">
<link type="text/css"
	  href="themes/green/style.css?v=<?php echo filemtime('themes/green/style.css'); ?>"
	  rel="stylesheet">
<script src="jquery/jquery.js"></script>
<script src="jquery/jquery.tablesorter.js"></script>
<script src="jquery/jquery.tablesorter.pager.js"></script>
<script src="jquery/jquery-ui.min.js"></script>
<script src="js/common.js"></script>

<title>HIS</title>

<style>
.center {
	margin: auto;
	width: 85%;
	padding: 5px;
}

.tooltip, .tooltip2 {
	position: relative;
	display: inline-block;
	border-bottom: 1px dotted black;
}

.tooltip .tooltiptext, .tooltip2 .tooltiptext2 {
	visibility: hidden;
	width: 240px;
	background-color: DarkCyan;
	color: #fff;
	text-align: center;
	border-radius: 6px;
	padding: 5px 0;
	/* Position the tooltip */
	position: absolute;
	z-index: 1;
}

.tooltip2 .tooltiptext2 {
	width: 75px;
	top: -5px;
	left: 150%;
}

.tooltip:hover .tooltiptext, .tooltip2:hover .tooltiptext2 {
	visibility: visible;
}

.total {
	background-color: #F0F8FF;
}

.turf {
	background-color: #32CD32;
}

.dirt {
	background-color: #F5F5DC;
}

table#navigator {
	text-align: center;
	margin-left: auto;
	margin-right: auto;
	border-spacing: 0px;
	border: 2px solid black;
	border-collapse: separate;
}

table#navigator td {
	border-right: 1px solid black;
	border-top: 1px solid black;
	border-collapse: separate;
	padding: 3px;
}

table#summary {
	border: 1px solid black;
	border-spacing: 0px;
	margin-left: auto;
	margin-right: auto;
}

table#summary thead {
	background-color: #B0C4DE;
	color: #000000;
}

table#summary td, th {
	border: 1px solid black;
	text-align: center;
	padding: 2px;
}

table#resultTable {
	font-size: 14px;
	border: 1px;
	border-collapse: separate;
	border-spacing: 2px;
	text-align: left;
	margin-left: auto;
	margin-right: auto;
}

table#resultTable td {
	padding: 1px;
}

table#topTenLists {
	border: 1px solid black;
	border-spacing: 0px;
	text-align: left;
	margin-left: auto;
	margin-right: auto;
}

table#topTenLists thead {
	background-color: #B0C4DE;
	color: #000000;
}

table#topTenLists td, th {
	border-top: 1px solid black;
	border-bottom: 1px solid black;
	border-right: 1px solid black;
	padding: 2px;
}

#topTenLists tr:nth-child(odd) td {
	background-color: PaleTurquoise;
} /*odd*/
#topTenLists tr:nth-child(even) td {
	background-color: OldLace;
} /* even*/
.nums, h1 {
	text-align: center;
}

.thick {
	border-left: 5px solid black;
}
</style>
<script>

  function showGraphic(type, id) {
    $("#individual_info").css('visibility', 'visible');
    uri=encodeURI("graphic_test.php?type=" + type + "&name=" + id);
    $("#individual_info").html('<img src="' + uri + '" >');
  }

  function clearInfo() {
    $('#race_summary').html("");
    $('#race_summary').css('visibility', 'hidden');
    $('#individual_info').html("");
    $('#undividual_info').css('visibility', 'hidden');
  }

</script>
</head>

<body>
	<h1 id="title">H.I.S.</h1>
	<table id='navigator'>
		<tr>
			<td><a href='add_winner.php'>Add</a></td>
			<td><a href='browse.php'>Browse</a></td>
			<td><a href='trends.php'>Trends</a></td>
			<td><a href='nextOutWinners.php'>NOW</a></td>
			<td><a id="scratches_url" target='_blank'>Scratches</a></td>
			<td><a id="site_url" target='_blank'>Site</a></td>
			<td><a target='_blank' href='http://www.drf.com'>DRF</a></td>
			<td><a target='_blank' href='http://www.twitter.com'>Twitter</a></td>
			<td><a target='_blank'
				href='https://www.brisnet.com/product/entries-programs'>Entries</a></td>
			<td><a target='_blank'
				href='https://www.brisnet.com/product/race-results'>Charts</a></td>
			<td><a target='_blank'
				href='http://www1.drf.com/formulator-web/#card-selector'>Formulator</a></td>
			<td><a target='_blank' href='https://www.nyrabets.com/#wagering'>Bet</a></td>
			<td><a target='_blank'
				href='http://www.brisnet.com/cgi-bin/static.cgi?page=stablealert'>Stable</a></td>
			<td><a target='_blank' href='https://play.drf.com/#/'>PPs</a></td>
			<td><a href='edit_defaults.php'>Settings</a></td>
			<td><a href='index.php?reset_session=1'>Reset</a></td>
		</tr>
	</table>
	<br>
	<table id='summary'>
		<thead>
			<tr>
				<th>Type</th>
				<th># of Days</th>
				<th>Last Date</th>
				<th>
					<div class='tooltip'>
						# of Races <span id='deadheats' class='tooltiptext'></span>
					</div>
				</th>
				<th>Avg Post</th>
				<th>Avg Field Size</th>
				<th>Avg Odds</th>
				<th># of Trainers</th>
				<th># of Jockeys</th>
				<th># of Horses</th>
				<th>
					<div class='tooltip'>
						Multi-Wins <span id='multiwinners' class='tooltiptext'></span>
					</div>
				</th>
			</tr>
		</thead>
		<tbody>
<?php
/*
 * --build stat line for display
 * statLine(connection.inc, turf [TRUE|FALSE])
 */
$horse_cnts ['Dirt'] = 0;
$horse_cnts ['Turf'] = 0;
$horse_cnts ['Total'] = 0;
$deadheat_cnt = 0;
function statLine($turf, $distance) {
	switch ($turf) {
		case 'TRUE' :
			$surface = 'Turf';
			$surface_class = 'turf';
			break;
		case 'FALSE' :
			$surface = 'Dirt';
			$surface_class = 'dirt';
			break;
		default :
			$surface = 'Total';
			$surface_class = 'total';
			break;
	}

	$meetObj = Meet::IdFactory( $_SESSION ['defaults'] ['race_meet_id'] );
	$stat_line = $meetObj->getSummaryStats ( [ 
			'turf' => $turf,
			'distance' => $distance
	] );

	global $horse_cnts;
	global $deadheat_cnt;
	if ($distance == 'total') {
		$horse_cnts [$surface] = $horse_cnts [$surface] + $stat_line ['horses'];
		$deadheat_cnt += ($stat_line ['deadheat'] / 2);
	}

	$multi_winners_count = $meetObj->getMultipleWinnerCount ( [ 
			'turf' => $turf
	] );

	// -- build stat line for display
	echo "
      <tr class=$surface_class>
        <td>$surface" . ($distance == 'total' ? '' : '/' . $distance) . "</td>
        <td>{$stat_line['dates']}</td>
        <td>{$stat_line['last_date']}</td>
        <td>{$stat_line['races']}</td>
        <td>{$stat_line['avg_post']}</td>
        <td>" . ($stat_line ['races'] > 0 ? round ( $stat_line ['sum_field_size'] / $stat_line ['races'], 2 ) : 0) . "</td>
        <td>{$stat_line['avg_odds']}</td>
        <td>{$stat_line['trainers']}</td>
        <td>{$stat_line['jockeys']}</td>
        <td>{$stat_line['horses']}</td>
        <td>$multi_winners_count</td>
      </tr>
      ";
}
// -- get last racing date and defaults
$lrdate = TB17::last_race_date ( $_SESSION ['defaults'] ['meet_filter'] );

// if meet has not started or no winners entered yet for meet, return/abort
if ($lrdate == '') {
	echo "
      </tbody>
    </body>
    </html>
      ";
	return;
}
// -- build major stat lines
statLine ( '', 'total' );

statLine ( 'FALSE', 'total' );
// statLine('FALSE', 'sprints');
// statLine('FALSE', 'routes');

statLine ( 'TRUE', 'total' );
// statLine('TRUE', 'sprints');
// statLine('TRUE', 'routes');
?>
      </tbody>
	</table>
	<br>

<?php
$meetObj = Meet::IdFactory( $_SESSION ['defaults'] ['race_meet_id'] );
// -- get top ten list data and send
$tj = $meetObj->getTopTen ( "jockey", 0, $_SESSION ['defaults'] ['start_date']);
$tt = $meetObj->getTopTen ( "trainer", 0, $_SESSION ['defaults'] ['start_date']);
$tjr = $meetObj->getTopTen ( "jockey", $_SESSION ['defaults'] ['past_days'] );
$ttr = $meetObj->getTopTen ( "trainer", $_SESSION ['defaults'] ['past_days'] );
echo "
    <div class='center'>
    <div style='float: left;' onmouseout=\"clearInfo()\">
    <table id='topTenLists'>
      <caption>Top 10 Wins Lists for {$_SESSION['defaults']['start_date']} thru $lrdate & Last {$_SESSION['defaults']['past_days']} Racing Days</caption>
      <thead>
        <th>Trainer</th>
        <th>Meet</th>
        <th>Trainer</th>
        <th>Last {$_SESSION['defaults']['past_days']}</th>
        <th class='thick'>Jockey</th>
        <th>Meet</th>
        <th>Jockey</th>
        <th>Last {$_SESSION['defaults']['past_days']}</th>
      </thead>
      <tbody>
    ";

for($row = 0; $row < count ( $tj ); ++ $row) {
	echo "
        <tr>
          <td onmouseover=\"showIndividualStats('trainer', '" . addslashes ( $tt [$row] ['name'] ) . "')\">{$tt[$row]['name']}</td>
          <td onmouseover=\"showGraphic('trainer', '" . addslashes ( $tt [$row] ['name'] ) . "')\"
              class='nums'>
              <div class='tooltip2'>{$tt[$row]['wins']}
                <span class='tooltiptext2'>{$tt[$row]['favs']} fav
                                       <br>{$tt[$row]['turfs']} turf
                                       <br>$" . round ( $tt [$row] ['avg_odds'], 1 ) . " odds
                </span>
              </div>
          </td>

          <td onmouseover=\"showIndividualStats('trainer', '" . addslashes ( $ttr [$row] ['name'] ) . "')\">{$ttr[$row]['name']}</td>
          <td onmouseover=\"showGraphic('trainer', '" . addslashes ( $ttr [$row] ['name'] ) . "')\"
              class='nums'>
            <div class='tooltip2'>{$ttr[$row]['wins']}
              <span class='tooltiptext2'>{$ttr[$row]['favs']} fav
                                     <br>{$ttr[$row]['turfs']} turf
                                     <br>$" . round ( $ttr [$row] ['avg_odds'], 1 ) . " odds
              </span>
            </div>
          </td>

          <td class='thick' 
              onmouseover=\"showIndividualStats('jockey', '" . addslashes ( $tj [$row] ['name'] ) . "')\">{$tj[$row]['name']}</td>
          <td 
              onmouseover=\"showGraphic('jockey', '" . addslashes ( $tj [$row] ['name'] ) . "')\"
              class='nums'>
            <div class='tooltip2'>{$tj[$row]['wins']}
              <span class='tooltiptext2'>{$tj[$row]['favs']} fav
                                     <br>{$tj[$row]['turfs']} turf
                                     <br>$" . round ( $tj [$row] ['avg_odds'], 1 ) . " odds
              </span>
            </div>
          </td>

          <td onmouseover=\"showIndividualStats('jockey', '" . addslashes ( $tjr [$row] ['name'] ) . "')\">{$tjr[$row]['name']}</td>
          <td onmouseover=\"showGraphic('jockey', '" . addslashes ( $tjr [$row] ['name'] ) . "')\"
              class='nums'>
            <div class='tooltip2'>{$tjr[$row]['wins']}
              <span class='tooltiptext2'>{$tjr[$row]['favs']} fav
                                     <br>{$tjr[$row]['turfs']} turf
                                     <br>$" . round ( $tjr [$row] ['avg_odds'], 1 ) . " odds
              </span>
            </div>
          </td>
        </tr>
      ";
}
echo "
      </tbody>
    </table>
    <br>
    </div>
    <div id='individual_info' style='float: left; visibility:hidden;'></div>
    </div>
    ";

// --- entry url
$url = "edit_winner.php?tb17_id";
// -- get results for last date run
$races = $meetObj->getRacesForDate ( $lrdate );
// -- build html result table
$date = new DateTime ( $lrdate, new DateTimeZone ( 'America/New_York' ) );
$chart_url = TB17::getEquibaseUrl($lrdate, $_SESSION['defaults']['track_id']);
echo "
      <div class='center'>
      <div style='clear: left; float: left;' onmouseout=\"clearInfo()\">
      <table id='resultTable' class='tablesorter'>
        <caption>Latest Racing Day Results (Date: $lrdate - " . $date->format ( 'l' ) . "  " . count ( $races ) . " races)  <a target='_blank' href='$chart_url'>Charts</a></caption>
      <thead>
        <tr>
          <th>Race</th>
          <th>Distance</th>
          <th>Class</th>
          <th>Horse</th>
          <th>Jockey</th>
          <th>Trainer</th>
        </tr>
      </thead>
      <tbody>
      ";

foreach ( $races as $tb17Obj ) {
	$class_sup = ($tb17Obj->sex == 'female' ? 'f ' : '') . $tb17Obj->age;
	$distance_sup = (strpos ( $tb17Obj->comment, "OTT" ) !== false ? 'OTT' : '');
	$horse_sup = ($tb17Obj->favorite == 'TRUE' ? '* ' : '') . (strpos ( $tb17Obj->comment, "FTS" ) !== false ? 'FTS' : '');
	echo "
        <tr onmouseover=\"showRaceSummaryInfo({$tb17Obj->tb17_id})\">
          <td class='nums'><a href='$url={$tb17Obj->tb17_id}'>{$tb17Obj->race}</a></td>
          <td class='nums " . ($tb17Obj->turf == 'TRUE' ? 'turf' : '') . "'>{$tb17Obj->distance} <sup>$distance_sup</sup></td>
          <td>{$tb17Obj->race_class} <sup>$class_sup</sup></td>
          <td>{$tb17Obj->horse} <sup>$horse_sup</sup></td>
          <td>{$tb17Obj->jockey}</td>
          <td>{$tb17Obj->trainer}</td>
         </tr>
      ";
}

echo "
        </tbody>
        </table>
        </div>
        <div id='race_summary' style='float: left; visibility:hidden;'></div>
      </div>
      <script>
        $(document).ready(function() {
          $('#scratches_url').attr('href','{$_SESSION['defaults']['scratches_url']}');
          $('#site_url').attr('href','{$_SESSION['defaults']['site_url']}');
          $('#title').text('{$_SESSION['defaults']['meet_name']}" . (DB_PRODUCTION == 1 ? '' : ' [' . DB_NAME . ']') . "');
          $('#resultTable').tablesorter({widgets: ['zebra']});
          $('#deadheats').text('" . ($deadheat_cnt / 2) . " races were deadheats');
          $('#multiwinners').text('" . (($horse_cnts ['Turf'] + $horse_cnts ['Dirt']) - $horse_cnts ['Total']) . " horses won on dirt & turf');
          $('#scratches_url').attr('href','{$_SESSION['defaults']['scratches_url']}');
          $('#site_url').attr('href','{$_SESSION['defaults']['site_url']}');
          $('#title').text('{$_SESSION['defaults']['meet_name']}" . (DB_PRODUCTION == 1 ? '' : ' [' . DB_NAME . ']') . "');
          // clear/hide dynamic eleemnts
          clearInfo();
        });
      </script>
    ";
?>
</body>
</html>