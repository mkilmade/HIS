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
<title>Find Next Out Winners</title>

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
<script type="text/javascript">
function clearRace() {
    $('#race_date').datepicker('setDate', '');
    $('#race').val('');
    $('#track_id').val('');
    $("#nextOutWinners").css('visibility', 'hidden');
}
  
function getNextOutWinners() {
    nextOutWinnersTable($("#race_date").val(),
                		$("#race").val(),
                		$("#track_id").val());
}
</script>
</head>

<body>
	<h2 id="body_title">Find Next Out Winners</h2>
	<table style="margin: auto;">
		<tr>
			<td><a href='index.php'>Home</a></td>
		</tr>
	</table>
	<br />
	<form>
		<table border=1 style="margin: auto;">
			<thead>
				<tr>
					<th>Date</th>
					<th>Track</th>
					<th>Race</th>
				</tr>
			
			
			<thead>
				<tr>
					<td><input type="date" id="race_date" name="race_date"></td>
					<td><input id="track_id" name="track_id" style="width: 60px;"></td>
					<td><input type="number" min="1" max="15" id="race" name="race"></td>
				</tr>
		
		</table>

		<table style="margin: auto;">
			<tr>
				<td><button id="find_btn" type="button"
						onclick="getNextOutWinners()">Find</button></td>
				<td><button id="clear_btn" type="button" onclick="clearRace()">Clear
						Race Info</button></td>
			
			
			<tr>
		
		</table>
	</form>
	<div id='nextOutWinners' style='visibility: hidden;'></div>
</body>
<script>
  $(document).ready(function() {
    $('#race_date').datepicker({
      currentText: 'Today',
      defaultDate: '2019-01-01',
      dateFormat: 'yy-mm-dd',
      showButtonPanel: true,
      onSelect: function(race_date) {
          getTrackId(race_date, '#track_id', '#race');
      }
    });  // datepicker
    
    $('#track_id, #race_date, #race').on('change', function() {
    	getNextOutWinners();
    });
    
    acDomainFields('#track_id');
  });  // ready
</script>

</html>