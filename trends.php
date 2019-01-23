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
	table#previousFinishTable, table#classTable, table#dayTable, table#classDetailTable {
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
	function getTrend(trend) {
	      if (trend.length == 0) { 
	          return;
	      }
	      // make sure trend and detail is not visibile and empty
	      $("#trendDetailDiv").css({'visibility': 'hidden', 'float': ''});
	      $("#trendDetailDiv").html('');
          $("#trendDiv").css('visibility', 'hidden');
	      $("#trendDiv").html('');
	      
	      var xmlhttp = new XMLHttpRequest();
	      xmlhttp.onreadystatechange = function() {
	          if (this.readyState == 4 && this.status == 200) {
	              $("#trendDiv").html(this.responseText);
	              $("#trendDiv").css('visibility', 'visible');
	          }
	      };
	      uri="getTrend.php?trend=" + trend;
	      xmlhttp.open("GET", encodeURI(uri), true);
	      xmlhttp.send();
	    }
    
    function setTrendDivStyle(type) {
        if (type == 'center') {
            //$('#trendDiv').attr('style', 'margin-right: auto; margin-left: auto;width: 1000px; visibility:hidden;');
            $('#trendDiv').css({
                'visibility': 'hidden',
                'float': '',
                'margin-right': 'auto',
                'margin-left': 'auto',
                'width': '1000px'
              });
        } else {
            // float type
        	//$('#trendDiv').attr('style', 'float: left; visibility: hidden;');
            $('#trendDiv').css({
                'visibility': 'hidden',
                'float': 'left',
                'margin-right': '',
                'margin-left': '',
                'width': ''
              });
        }
    }
    
  	function getTrendDetail(trend, param) {
        if (trend.length == 0) { 
            return;
        }
        $("#trendDetailDiv").css('visibility', 'hidden');
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $("#trendDetailDiv").html(this.responseText);
                $("#trendDetailDiv").css('visibility', 'visible');
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
	<table id="navigator">
		<caption>(hover trend to see trending info)</caption>
		<tr>
			<td><a href='index.php'>Home</a></td>
			<td onmouseover="getTrend('classTally')">Class Tally</td>
			<td onmouseover="getTrend('keyRaces')">Key Racess</td>
			<td onmouseover="getTrend('multipleWins')">Multiple Wins</td>
			<td onmouseover="getTrend('previouslyRanAtMeet')">Previously Ran At
				Meet Before Win</td>
			<td onmouseover="getTrend('previousRaceAtMeetPerCard')">Previous Race
				At Meet Per Card</td>
			<td onmouseover="getTrend('previousTrackWins')">Previous Track Wins</td>
			<td onmouseover="getTrend('previousFinishTally')">Previous Finish
				Tally</td>
		</tr>
	</table>
	<br>
	<div id='trendDetailDiv'></div>
	<div id='trendDiv'></div>
</body>
<script>
    $(document).ready(function() {
    	getTrend('keyRaces');
    });
</script>

</html>
