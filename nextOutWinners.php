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
<title>Find Next Out Wins</title>

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
function clearRace() {
    $('#race_date').datepicker('setDate', '');
    $('#race').val('');
    $('#track_id').val('');
    $("#nextOutWinners").css('visibility', 'hidden');
  }
function getNextOutWinners() {
    var race_date=$("#race_date").val(),
        race=$("#race").val(),
        track_id=$("#track_id").val();

    // make ajax call if all 'previous fields are filled in (except 'finish')
    if (race_date != "" && race != "" && track_id != "") {
    
      // build query info for GET
      var queryData = new Object();
      queryData.type = 'next_out_winners';
      queryData.race_date = race_date;
      queryData.race = race;
      queryData.track_id = track_id;
    
      // build settings/options for $.ajax call
      var options = new Object();
      options.data = queryData;
      options.dataType = "json";
      options.method = "GET";
      options.success = function(response, status, xhr) {
    	$("#nextOutWinners").css('visibility', 'visible');
        $("#nextOutWinners").html(response.html);
      }
      options.error = function(xhr, status, errorThrown) {
        console.log("An error has occcured in 'getNextOutWinners' function:");
        console.log("       Status: " + xhr.status + " - " + xhr.statusText);
        console.log("Response Text: " + xhr.responseText);
      }
      options.url = "getHisInfo.php";
    
      $.ajax(options);
    } else {
    	$("#nextOutWinners").css('visibility', 'visible');
       	$("#nextOutWinners").html(" (Next Out Winners: tbd)");
    }
 }
</script>
<body>
	<h2 id="body_title">Find Next Out Winners</h2>
	<table>
		<tr>
			<td><a href='index.php'>Home</a></td>
		</tr>
	</table>
	<br />
		<strong>Race Info:</strong>
		<form>
		<table border=1>
			<thead>
				<tr>
					<th>Date</th>
					<th>Race</th>
					<th>Track ID</th>
				</tr>
			<thead>
				<tr>
					<td><input type="date" id="race_date" name="race_date"></td>
					<td><input type="text" id="race" name="race"></td>
					<td><input type="text" id="track_id" name="track_id"></td>
				</tr>
		
		</table>
		<table>
			<tr>
				<td><button type="button" onclick="getNextOutWinners()">Find</button></td>
				<td><button type="button" onclick="clearRace()">Clear Race Info</button></td>
			<tr>
		
		</table>
		</form>
  <div id='nextOutWinners' style='float: left; visibility:hidden;'></div>
</body>
<script>
  $(document).ready(function() {
    $('#race_date').datepicker({
      currentText: 'Today',
      defaultDate: 0,
      dateFormat: 'yy-mm-dd',
      showButtonPanel: true
    });
  });
</script>

</html>

