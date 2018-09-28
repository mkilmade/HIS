<?php 
// add_winner.php script mjk 4/19/18
    // form to log winning entry into the tbd.tb17 table
    session_start();
    require_once('includes/config.inc.php');
    require_once('includes/connection.inc.php');;
    $conn = new Connection();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link href="jquery/jquery-ui.min.css" rel="stylesheet">
<script src="jquery/jquery.js"></script>
<script src="jquery/jquery.tablesorter.js"></script>
<script src="jquery/jquery-ui.min.js"></script>
<script src="js/common.js"></script>
<title>Add Winner</title>
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

  <?php require_once('includes/entry_input.inc.html'); ?>

 </fieldset>

		<p style="text-align: left">
			<input type="submit" name="submit" value="INSERT">
		</p>

	</form>

</body>
<script>
  $(document).ready(function() {
    setupCommonFields();
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
<?php
    // -- get last race date a& next race #
    $last_race_date = $conn->last_race_date();
    $next_race = $conn->last_race($last_race_date) + 1;
    $conn->close();
    echo "
    var last_race_date = '$last_race_date',
        next_race = '$next_race',
        current_track_id = '{$conn->defaults['track_id']}',
        previous_track_id = '{$conn->defaults['previous_track_id']}',
        dirt_track_condition = '{$_SESSION['dirt_track_condition']}',
        turf_track_condition = '{$_SESSION['turf_track_condition']}';
        
"
?>
    $('#race_date').datepicker('setDate', last_race_date);
    $('#race').val(next_race);
    $('#track_id').val(current_track_id);
    $('#previous_track_id').val(previous_track_id);
    $('#track_condition').val(dirt_track_condition);
    $('input[name=turf]:radio').on('change', function(e) {
        turf=$('input[name=turf]:checked', '#addForm');
        $('#track_condition').val(turf.val()=='TRUE' ? turf_track_condition : dirt_track_condition);
    });    
  }); // finish .ready function
</script>
</html>