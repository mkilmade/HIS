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
<title>Trends</title>

<style>
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

.turf {
	background-color: #32CD32;
}

.dirt {
	background-color: #F5F5DC;
}

h2 {
	text-align: left;
}

table#keyTable, table#trackTable, table#multiWinsTable, table#previousMeetDateCountTable,
	table#previousFinishTable, table#classTable, table#dayTable, table#classDetailTable
	{
	border: 1px;
	border-collapse: separate;
	border-spacing: 2px;
	text-align: center;
	margin: auto;
}

table#keyTable, table#trackTable td {
	padding: 0px;
}
</style>
<script>
  	function getClassTrendDetail(trend, param) {
        if (trend.length == 0) { 
            return;
        }
        $("#classTrendDetailDiv").css('visibility', 'hidden');
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $("#classTrendDetailDiv").html(this.responseText);
                $("#classTrendDetailDiv").css('visibility', 'visible');
            }
        };
        uri="getTrendDetail.php?trend=" + trend + "&param=" + param;
        xmlhttp.open("GET", encodeURI(uri), true);
        xmlhttp.send();
      }
  </script>

</head>
<body>
	<h2 id="body_title" style="text-align: center;">Browse Trends And Stats</h2>
	<table style="margin: auto;">
		<tr>
			<td><a href='index.php'>Home</a></td>
		</tr>
	</table>
	<br />
	<div id="tabs">
	  <ul>
	    <li><a href="getTrend.php?trend=keyRaces">Keys</a></li>
	    <li><a href="getTrend.php?trend=classTally">Class</a></li>
	    <li><a href="getTrend.php?trend=multipleWins">Multi-Wins</a></li>
	    <li><a href="getTrend.php?trend=previousTrackWins">Tracks</a></li>
	    <li><a href="getTrend.php?trend=previouslyRanAtMeet">Ran At Meet</a></li>
	    <li><a href="getTrend.php?trend=previousRaceAtMeetPerCard">Per Card</a></li>
	    <li><a href="getTrend.php?trend=previousFinishTally">Prev Finish</a></li>
	  </ul>
	</div>
</body>
<script>
    $(document).ready(function() {
    	$( "#tabs" ).tabs({
    		  event: "mouseover",
    		  heightStyle: "content"
    	});
    });
</script>

</html>
