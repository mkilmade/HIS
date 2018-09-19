<?php 
  session_start(); 
  require_once('includes/config.inc.php');
  require_once('includes/connection.inc.php');
  $conn = new Connection();
?>
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
  <title>Trends</title>

  <style>
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
</head>
<body>
  <h2 id="body_title">Browse Trends And Stats</h2>
   <table>
    <tr>
      <td><a href='index.php'>Home</a></td>
    </tr>
  </table>
  <br/>
  <?php
     //=============== build key race table (#keyTable) ================
     require_once('includes/trends/key_races.php');

     //=============== get multiple winner at meet (#multiWinsTable) ================
     require_once('includes/trends/multiple_wins_at_meet.php');

     //=============== get previous track win counts (#trackTable) ===============
     require_once('includes/trends/previousTrackWins.inc.php');

     //=============== get previous race at meet winner counts by race_date (#previousMeetDateCountTable) ================
     require_once('includes/trends/previously_ran_by_date.php');

     //=============== get previous race finish position tally (#previousFinishTable) ================
     require_once('includes/trends/previousFinishTally.inc.php');

     //=============== get previous race at meet winners (#previouslyRanAtMeetWinTable) ================
     require_once('includes/trends/previously_ran_at_meet.php');

     //=============== get class tally for meet (#classTable) ================
     require_once('includes/trends/class_tally_for_meet.php');

    $conn->close();

    echo "
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
    ";
  ?>
</body>
</html>
