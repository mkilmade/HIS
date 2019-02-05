<?php
// add_winner.php script mjk 4/19/18
// form to log winning entry into the tbd.tb17 table
require_once ('includes/envInit.inc.php');
?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link href="jquery/jquery-ui.min.css" rel="stylesheet">
<link type="text/css"
	  href="themes/green/style.css?v=<?php echo filemtime('themes/green/style.css'); ?>"
	  rel="stylesheet">
<script src="jquery/jquery.js"></script>
<script src="jquery/jquery.tablesorter.js"></script>
<script src="jquery/jquery.tablesorter.pager.js"></script>
<script src="jquery/jquery-ui.min.js"></script>
<script src="js/common.js"></script>
<title>Add Winner</title>
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

table#nowTable {
	border: 1px;
	border-collapse: separate;
	border-spacing: 2px;
	text-align: center;
}

table#nowTable td {
	padding: 0px;
}
</style>

</head>

<body>
	<table>
		<tr>
			<td><a href='index.php'>Home</a></td>
			<td><a href='browse.php'>Browse</a></td>
		</tr>
	</table>
	<br>

	<form id="addForm" action="insert_winner.php" method="post">

		<fieldset style="width: 425px; background-color: Azure">
			<legend style="text-align: center">Add Entry</legend>

  <?php require_once('entry_input.inc.html'); ?>

 </fieldset>

		<p style="text-align: left">
			<input type="submit" name="submit" value="INSERT">
		</p>

	</form>

</body>
<script>
  $(document).ready(function() {
<?php
// -- get last race date a& next race #
$last_race_date = TB17::last_race_date ( $_SESSION ['defaults'] ['meet_filter'] );
$next_race = TB17::last_race ( $last_race_date, $_SESSION ['defaults'] ['meet_filter'] ) + 1;
echo "
    var last_race_date = '$last_race_date',
        next_race = '$next_race',
        current_track_id = '{$_SESSION['defaults']['track_id']}',
        default_previous_date = '{$_SESSION['defaults']['default_previous_date']}',
        dirt_track_condition = '{$_SESSION['dirt_track_condition']}',
        turf_track_condition = '{$_SESSION['turf_track_condition']}',
        age = '{$_SESSION['defaults']['age']}';
        
    "?>
    setupCommonFields(default_previous_date);
    // set race number to 1 if date is new or to appropriate race # if race date on file
    // (most helpful when adding a new race date)
    $('#race_date').on('change',function(e) {
        race_date_trigger();
    });
    
    // set favorite to true if less than threshold (1.5)
    $('#odds').on('change',function(e) {
      if ($('#odds').val() < 1.5) {
        $('input:radio[name="favorite"][value="TRUE"]').prop('checked',true);
      }
    });
    $('#age').val(age);
    $('#race_date').datepicker('setDate', last_race_date);
    $('#race').val(next_race);
    $("#distance").focus();
    $('#track_id').val(current_track_id);
    $('#track_condition').val(dirt_track_condition);
    $('input[name=turf]:radio').on('change', function(e) {
        turf=$('input[name=turf]:checked', '#addForm');
        $('#track_condition').val(turf.val()=='TRUE' ? turf_track_condition : dirt_track_condition);
    });    
  }); // finish .ready function
</script>
</html>