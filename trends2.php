<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
  <link type="text/css" href="jquery/jquery-ui.min.css" rel="stylesheet">
  <link type="text/css" href="themes/green/style.css?v=<?php echo filemtime('themes/green/style.css'); ?>" rel="stylesheet">
  <script src="jquery/jquery.js"></script>
  <script src="jquery/jquery.tablesorter.js"></script>
  <script src="jquery/jquery.tablesorter.pager.js"></script>
  <script src="jquery/jquery-ui.min.js"></script>
  <title id="title">Trends</title>

  <style>
    table#navigator {
      text-align:center;
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

    .turf {background-color:#32CD32;}
    .dirt {background-color:#F5F5DC;}

    h2 {text-align:left;}

    table#keyTable, 
    table#trackTable,
    table#multiWinsTable,
    table#previousMeetDateCountTable,
    table#previousFinishTable,
    table#classTable  {
      border: 1px;
      border-collapse: separate;
      border-spacing: 2px;
      text-align:center;
    }

    table#keyTable,
    table#trackTable td {
      padding: 0px;
    }
  </style>
  <script>
  	function getTrend(trend) {
      //console.log("in func");
      if (trend.length == 0) { 
          return;
      }
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
  </script>
  
</head>
<body>
  <h2 id="body_title">Browse Trends And Stats</h2>
  <table id="navigator">
    <caption>(hover trend to see trending info)</caption>
    <tr>
      <td><a href='index.php'>Home</a></td>
      <td onmouseover="getTrend('keyRaces')">Key Racess</td>
      <td onmouseover="getTrend('multipleWins')">Multiple Wins</td>
      <td onmouseover="getTrend('previouslyRanAtMeet')">Previously Ran At Meet Before Win</td>
      <td onmouseover="getTrend('previousRaceAtMeetPerCard')">Previous Race At Meet Per Card</td>
    </tr>
  </table>
  <br>
  <div id='trendDiv' style='float: left; visibility:hidden;'></div>
</body>

<script>
  $(document).ready(function() {

    $('#keyTable').tablesorter({
       widgets: ['zebra'],
       headers: {
         1: {
          sorter: false
         },
         4: {
          sorter: false
         }
       }
    });

    $('#trackTable').tablesorter({
      widgets: ['zebra']
    });

    $('#multiWinsTable').tablesorter({
      widgets: ['zebra']
    });

    $('#previouslyRanAtMeetWinTable').tablesorter({
      widgets: ['zebra']
    });

    $('#previousFinishTable').tablesorter({
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

    $('#previousMeetDateCountTable').tablesorter({
        widgets: ['zebra'],
        headers: {
          1: {
            sorter: false
          }
        }
    });

    $('#classTable').tablesorter({
      widgets: ['zebra']
    });

  });
</script>
</html>
