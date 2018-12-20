<?php
require_once ('includes/envInit.inc.php');
?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link type="text/css" href="jquery/jquery-ui.min.css" rel="stylesheet">
<link type="text/css"
	  href="themes/green/style.css?v=<?php echo filemtime('themes/green/style.css'); ?>"
	  rel="stylesheet">
<script src="jquery/jquery.js"></script>
<script src="jquery/jquery.tablesorter.js"></script>
<script src="jquery/jquery.tablesorter.pager.js"></script>
<script src="jquery/jquery-ui.min.js"></script>
<script src="js/common.js"></script>
<title>Browse Meet Results</title>

<style>
.turf {
	background-color: #32CD32;
}

.dirt {
	background-color: #F5F5DC;
}

h2 {
	text-align: center;
}

table#resultTable {
	border: 1px;
	border-collapse: separate;
	border-spacing: 2px;
	text-align: center;
}

table#resultTable td {
	padding: 0px;
}
</style>
</head>
<script>
  function clearFilters() {
    $('#race_date').datepicker('setDate', '');
    $('#trainer').val('');
    $('#jockey').val('');
    $('#horse').val('');
  }
</script>
<body>
	<h2 id="body_title">Browse/Edit Meet Winners</h2>
	<table>
		<tr>
			<td><a href='index.php'>Home</a></td>
			<td><a href='add_winner.php'>Add Winner</a></td>
		</tr>
	</table>
	<br />
	<form action="browse.php" method="post">
		<strong>Filters:</strong>
		<table border=1>
			<thead>
				<tr>
					<th>Date</th>
					<th>Trainer</th>
					<th>Jockey</th>
					<th>Horse</th>
				</tr>
			
			
			<thead>
				<tr>
					<td><input type="date" id="race_date" name="filterrace_date"></td>
					<td><input type="text" id="trainer" name="filtertrainer"></td>
					<td><input type="text" id="jockey" name="filterjockey"></td>
					<td><input type="text" id="horse" name="filterhorse"></td>
				</tr>
		
		</table>
		<table>
			<tr>
				<td><input id="apply_button_id" type="submit" name="submit"
					value="Apply Filter"></td>
				<td><button type="button" onclick="clearFilters()">Clear Filters</button></td>
			
			
			<tr>
		
		</table>
	</form>
  
<?php

// -- find last race date if no filters set
$post = $_POST;
if (! isset ( $post ['filterrace_date'] ) && 
	! isset ( $post ['filtertrainer'] ) &&
	! isset ( $post ['filterjockey'] ) &&
	! isset ( $post ['filterhorse'] )) {
	$rdate = TB17::last_race_date ();
	$post ['filterrace_date'] = $rdate;
} else {
	$rdate = $post ['filterrace_date'];
}
$filterrace_date = (isset ( $post ['filterrace_date'] ) ? $post ['filterrace_date'] : '') . '%';
$filtertrainer = (isset ( $post ['filtertrainer'] ) ? $post ['filtertrainer'] : '') . '%';
$filterjockey = (isset ( $post ['filterjockey'] ) ? $post ['filterjockey'] : '') . '%';
$filterhorse = (isset ( $post ['filterhorse'] ) ? $post ['filterhorse'] : '') . '%';

// -- get results for filters
$races = TB17::getBrowseRequestResults ( [ 
		'race_date' => $filterrace_date,
		'trainer' => $filtertrainer,
		'jockey' => $filterjockey,
		'horse' => $filterhorse
], $_SESSION ['defaults'] ['meet_filter'] );

$caption = $_SESSION ['defaults'] ['track_name'] . " Results (" . count ( $races ) . " races)";
?>

<div style='overflow-x: auto;'>
		<table id='resultTable' class='tablesorter'
			style='width: 950px; font-size: 14px'>
			<caption id='caption'></caption>
			<thead>
				<tr>
					<th>id</th>
					<th>Date</th>
					<th>Day</th>
					<th>Race</th>
					<th>Distance</th>
					<th>Turf</th>
					<th>Class</th>
					<th>Sex</th>
					<th>Age</th>
					<th>Horse</th>
					<th>Jockey</th>
					<th>Trainer</th>
				</tr>
			</thead>
			<tbody>
    
<?php
// -- build html result table
foreach ( $races as $race ) {
	$date = new DateTime ( $race->race_date, new DateTimeZone ( 'America/New_York' ) );
	$chart_file = "http://www.equibase.com/premium/chartEmb.cfm?track={$race->track_id}&raceDate=" . $date->format ( "m/d/y" ) . "&cy=USA&rn={$race->race}";
	echo "<tr>";
	echo "<td><a href='edit_winner.php?tb17_id={$race->tb17_id}'>{$race->tb17_id}</a></td>";
	echo "<td>{$race->race_date}</td>";
	echo "<td>" . $date->format ( 'l' ) . "</td>";
	echo "<td><a target='_blank' href='$chart_file'>{$race->race}</a></td>";
	echo "<td>{$race->distance}</td>";
	echo "<td class='" . ($race->turf == 'TRUE' ? 'turf\'>Turf' : '\'>Dirt') . "</td>";
	echo "<td>{$race->race_class}</td>";
	echo "<td>{$race->sex}</td>";
	echo "<td>{$race->age}</td>";
	echo "<td>{$race->horse}</td>";
	echo "<td>{$race->jockey}</td>";
	echo "<td>{$race->trainer}</td>";
	echo "</tr>";
}
?>
</tbody>
		</table>
	</div>
	<script>
      $(document).ready(function() {
        $('input[type=text]').keypress(function(e){
           if(e.keyCode==13) {
             $('#apply_button_id').click();
           }
        });

        $('#resultTable').tablesorter({widgets: ['zebra']});
        acDomainFields('#horse, #trainer, #jockey');

        $('#race_date').datepicker({
          currentText: 'Today',
          defaultDate: 0,
          dateFormat: 'yy-mm-dd',
          showButtonPanel: true
        });
<?php
echo "
        $('#race_date').datepicker('setDate','$rdate');
        $('#trainer').val('" . addslashes ( isset ( $post ['filtertrainer'] ) ? $post ['filtertrainer'] : '' ) . "');
        $('#jockey').val('" . addslashes ( isset ( $post ['filterjockey'] ) ? $post ['filterjockey'] : '' ) . "');
        $('#horse').val('" . addslashes ( isset ( $post ['filterhorse'] ) ? $post ['filterhorse'] : '' ) . "');
        document.title='{$_SESSION['defaults']['meet_name']} (Browse)';
        $('#body_title').text('{$_SESSION['defaults']['meet_name']}');
        $('#caption').text('$caption');
      });
    ";
?>
</script>
</body>
</html>
