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
    // set race number to 1 if date is chenged (helpful when adding a new race date
    $('#race_date').on('change',function(e) {
    	var race_date=$("#race_date").val();
    	
        // build request for GET
        var request = new Object();
        request.type = 'next_race';
        request.race_date = race_date;

        // build settings/options for $.ajax call
        var options = new Object();
        options.data = request;
        options.dataType = "json";
        options.method = "GET";
        options.success = function(response, status, xhr) {
          $("#race").val(response.next_race);
        }
        options.error = function(xhr, status, errorThrown) {
          console.log("An error has occcured in request for next race #:");
          console.log("       Status: " + xhr.status + " - " + xhr.statusText);
          console.log("Response Text: " + xhr.responseText);
        }
        options.url = "getHisInfo.php";

        $.ajax(options);
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
        $('#race_date').datepicker('setDate', '$last_race_date');
        $('#race').val('$next_race');
        $('#track_id').val('{$conn->defaults['track_id']}');
        $('#previous_track_id').val('{$conn->defaults['previous_track_id']}');
        $('#track_condition').val('{$_SESSION['dirt_track_condition']}');
        $('input[name=turf]:radio').on('change', function(e) {
          turf=$('input[name=turf]:checked', '#addForm');
          $('#track_condition').val(turf.val()=='TRUE' ? '{$_SESSION['turf_track_condition']}':'{$_SESSION['dirt_track_condition']}');
        });
    
        ";
?>

}); // finish .ready function
</script>
</html>