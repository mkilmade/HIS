<?php
    ini_set('session.gc_maxlifetime',3600);
    session_start();

    require_once('includes/config.inc.php');
    require_once('includes/connection.inc.php');
    spl_autoload_register(function ($class) {
    	require_once 'classes/' . $class . '.class.php';
    });;
    $conn = new Connection();
    //print_r($_SESSION);

    // initialize track conditions to have more accurate input when
    // adding winner entries by race # (lower to higher)
    if(!isset($_SESSION['dirt_track_condition'])) $_SESSION['dirt_track_condition'] = "Fast";
    if(!isset($_SESSION['turf_track_condition'])) $_SESSION['turf_track_condition'] = "Firm";

    /*
     --build stat line for display
     statLine(connection.inc, turf [TRUE|FALSE])
     */
    $horse_cnts['Dirt']=0;
    $horse_cnts['Turf']=0;
    $horse_cnts['Total']=0;
    $deadheat_cnt=0;
    
    function statLine(&$conn, $turf, $distance) {
        
        switch($turf) {
            case 'TRUE':
                $surface='Turf';
                $surface_class='turf';
                break;
            case 'FALSE':
                $surface='Dirt';
                $surface_class='dirt';
                break;
            default:
                $surface='Total';
                $surface_class='total';
                break;
        }
        // -- build basic stats query
        $query="SELECT
                 COUNT(DISTINCT race_date),
                 MAX(race_date),
                 COUNT(DISTINCT race_date, race),
                 SUM(IF(comment LIKE 'dead%',1,0)) as deadheat,
                 TRUNCATE(AVG(post_position),1),
                 SUM(IF(comment LIKE 'dead%',field_size/2,field_size)),
                 TRUNCATE(AVG(IF(odds>0,odds,NULL)),2),
                 COUNT(DISTINCT trainer),
                 COUNT(DISTINCT jockey),
                 COUNT(DISTINCT horse)
                FROM tb17
                WHERE {$conn->defaults['meet_filter']} and horse <> ''"; // don't use if no horse enter yet
        
        // -- add WHERE clause
        if ($surface <> 'Total') {
            $query .= " AND turf='$turf'";
            if ($distance <> 'total') {
                $query .= "AND distance ".($distance=='sprints' ? "<'8'" : ">='8'");
            }
        }
        // will only get 1 entry and 'LIMIT' 1 is more efficient
        $query .= " LIMIT 1";
        
        // -- run query
        $stmt = $conn->db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($dates,
            $last_date,
            $races,
            $deadheat,
            $avg_post,
            $sum_field_size,
            $avg_odds,
            $trainers,
            $jockeys,
            $horses);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
        
        global $horse_cnts;
        global $deadheat_cnt;
        
        if ($distance=='total') {
            $horse_cnts[$surface] = $horse_cnts[$surface] + $horses;
            $deadheat_cnt += ($deadheat/2);
        }
        // get multiple winners count
        $qry="SELECT
               COUNT(*) as count
              FROM (SELECT COUNT(*) AS Wins,
                      horse
                    FROM tb17
                    WHERE {$conn->defaults['meet_filter']} AND horse <> ''"; // don't use if no horse enter yet
        // -- add to derived WHERE clause
        if ($surface <> 'Total') {
            $qry .= " AND turf='$turf'";
        }
        $qry .= " GROUP BY horse) AS multi_winners_count
         WHERE Wins > '1' LIMIT 1";
        
        //echo "<br>$sum_field_size:$races";
        // -- run query
        $stmt2 = $conn->db->prepare($qry);
        $stmt2->execute();
        $stmt2->store_result();
        $stmt2->bind_result($multi_winners_count);
        $stmt2->fetch();
        $stmt2->free_result();
        $stmt2->close();
        
        
        // -- build stat line for display
        echo "
      <tr class=$surface_class>
        <td>$surface".($distance=='total' ? '' : '/'.$distance)."</td>
        <td>$dates</td>
        <td>$last_date</td>
        <td>$races</td>
        <td>$avg_post</td>
        <td>".($races>0 ? round($sum_field_size/$races,2) : 0)."</td>
        <td>$avg_odds</td>
        <td>$trainers</td>
        <td>$jockeys</td>
        <td>$horses</td>
        <td>$multi_winners_count</td>
      </tr>
      ";
    }
    
    function topTen(&$conn, $type, $as_of_date, $days) {
        // -- build basic stats query
        if ($days>0) {
            $date= new DateTime($as_of_date);
            $date->sub(new DateInterval('P'.$days.'D'));
        } else {
            $date= new DateTime($conn->defaults['start_date']);
            $date->sub(new DateInterval('P1D'));
        }
        $diff=$date->format('Y-m-d');
        
        $query="SELECT
                  $type as name,
                  COUNT(*) as wins,
                  SUM(IF(favorite='TRUE',1,0)) as favs,
                  SUM(IF(turf='TRUE',1,0)) as turfs,
                  AVG(IF(odds<>0.0,odds,NULL)) as avg_odds
                FROM tb17
                WHERE race_date > ? AND trainer <> '' AND jockey <> '' AND {$conn->defaults['meet_filter']}
                GROUP BY $type
                ORDER BY wins DESC, $type
                LIMIT 10
              ";
          
          // -- run query
          $stmt = $conn->db->prepare($query);
          $stmt->bind_param('s', $diff);
          $stmt->execute();
          $result=$stmt->get_result();
          $rows=$result->fetch_all(MYSQLI_ASSOC);
          $stmt->free_result();
          $stmt->close();
          //print_r($rows);
          return $rows;
    }
    
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
  <link type="text/css" href="jquery/jquery-ui.min.css" rel="stylesheet">
  <link type="text/css" href="themes/green/style.css?v=<?php echo filemtime('themes/green/style.css'); ?>" rel="stylesheet">
  <script src="jquery/jquery.js"></script>
  <script src="jquery/jquery.tablesorter.js"></script>
  <script src="jquery/jquery.tablesorter.pager.js"></script>
  <script src="jquery/jquery-ui.min.js"></script>
  <script src="js/common.js"></script>

  <title>HIS</title>

  <style>
    .center {
        margin: auto;
        width: 85%;
        padding: 5px;
    }

   .tooltip, .tooltip2 {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted black;
    }

    .tooltip .tooltiptext, .tooltip2 .tooltiptext2 {
        visibility: hidden;
        width: 240px;
        background-color: DarkCyan;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
    }

    .tooltip2 .tooltiptext2 {
        width: 75px;
        top:-5px;
        left: 150%;
    }

    .tooltip:hover .tooltiptext, .tooltip2:hover .tooltiptext2 {
        visibility: visible;
    }

    .total {
      background-color:#F0F8FF;
    }
    .turf {
      background-color:#32CD32;
    }
    .dirt {
      background-color:#F5F5DC;
    }

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

    table#summary {
      border: 1px solid black;
      border-spacing: 0px;
      margin-left: auto;
      margin-right: auto;
    }
    table#summary thead {
      background-color:#B0C4DE;
      color:#000000;
    }
    table#summary td, th {
      border: 1px solid black;
      text-align:center;
      padding:2px;
    }

    table#resultTable {
      font-size: 14px;
      border: 1px;
      border-collapse: separate;
      border-spacing: 2px;
      text-align:left;
      margin-left: auto;
      margin-right: auto;
    }
    table#resultTable td {
      padding: 1px;
    }

    table#topTenLists {
      border: 1px solid black;
      border-spacing: 0px;
      text-align:left;
      margin-left: auto;
      margin-right: auto;
    }
    table#topTenLists thead {
      background-color:#B0C4DE;
      color:#000000;
    }
    table#topTenLists td, th {
      border-top: 1px solid black;
      border-bottom: 1px solid black;
      border-right: 1px solid black;
      padding:2px;
    }
    #topTenLists tr:nth-child(odd) td {
      background-color: PaleTurquoise;
    } /*odd*/
    #topTenLists tr:nth-child(even) td {
        background-color: OldLace;
    } /* even*/
    .nums, h1 {
      text-align:center;
    }
    .thick {
      border-left: 5px solid black;
    }

  </style>
 <script>

  function showGraphic(type, id) {
    $("#individual_info").css('visibility', 'visible');
    uri=encodeURI("graphic_test.php?type=" + type + "&name=" + id);
    $("#individual_info").html('<img src="' + uri + '" >');
  }

  function clearInfo() {
    $('#race_info').html("");
    $('#race_info').css('visibility', 'hidden');
    $('#individual_info').html("");
    $('#undividual_info').css('visibility', 'hidden');
  }

</script>
</head>

<body>
    <h1 id="title">H.I.S.</h1>
    <table id='navigator'>
        <tr>
          <td><a href='add_winner.php'>Add Winner</a></td>
          <td><a href='browse.php'>Browse</a></td>
          <td><a href='search.php'>Search</a></td>
          <td><a id="scratches_url" target='_blank'>Scratches</a></td>
          <td><a id="site_url" target='_blank'>Site</a></td>
          <td><a target='_blank' href='http://www.drf.com'>DRF</a></td>
          <td><a target='_blank' href='http://www.twitter.com'>Twitter</a></td>
          <td><a target='_blank' href='https://www.brisnet.com/product/entries-programs'>Entries</a></td>
          <td><a target='_blank' href='https://www.brisnet.com/product/race-results'>Charts</a></td>
          <td><a target='_blank' href='http://www1.drf.com/formulator-web/#card-selector'>Formulator</a></td>
          <td><a target='_blank' href='https://www.nyrabets.com/#wagering'>NYRA Bets</a></td>
          <td><a target='_blank' href='http://www.brisnet.com/cgi-bin/static.cgi?page=stablealert'>Stable</a></td>
          <td><a target='_blank' href='https://play.drf.com/#/'>PPs</a></td>       
          <td><a href='trends.php'>Trends/Stats</a></td>
          <td><a href='nextOutWinners.php'>NOW</a></td>
          <td><a href='edit_defaults.php'>Settings</a></td>
        </tr>
    </table>
    <br>
    <table id='summary'>
      <thead><tr>
        <th>Type</th>
        <th># of Days</th>
        <th>Last Date</th>
        <th>
          <div class='tooltip'># of Races
            <span id='deadheats' class='tooltiptext'></span>
          </div>
       </th>
        <th>Avg Post</th>
        <th>Avg Field Size</th>
        <th>Avg Odds</th>
        <th># of Trainers</th>
        <th># of Jockeys</th>
        <th># of Horses </th>
        <th>
          <div class='tooltip'>Multi-Wins
            <span id='multiwinners' class='tooltiptext'></span>
          </div>
        </th>
      </tr></thead>
      <tbody>
<?php
    // -- get last racing date and defaults
$lrdate=TB17::last_race_date($conn->defaults['meet_filter']);

    // if meet has not started or no winners entered yet for meet, return
    if ($lrdate=='') {
      echo "
      </tbody>
    </body>
        <script>
          $(document).ready(function() {
            $('#scratches_url').attr('href','{$conn->defaults['scratches_url']}');
            $('#site_url').attr('href','{$conn->defaults['site_url']}');
            $('#title').text('{$conn->defaults['meet_name']}".(DB_PRODUCTION == 1 ? '' :' ['.DB_NAME.']')."');
           });
        </script>
    </html>
      ";
      return;
    }
    // -- build major stat lines
    statLine($conn, '', 'total');

    statLine($conn, 'FALSE', 'total');
    //statLine($conn, 'FALSE', 'sprints');
    //statLine($conn, 'FALSE', 'routes');

    statLine($conn, 'TRUE', 'total');
    //statLine($conn, 'TRUE', 'sprints');
    //statLine($conn, 'TRUE', 'routes');
?>

      </tbody>
    </table>
    <br>

<?php
    // -- build top ten lists

    // -- get top ten list data and send
    $topJockeys=topTen($conn, "jockey", $lrdate, 0);
    $topTrainers=topTen($conn, "trainer", $lrdate, 0);
    $topJockeysRecent=topTen($conn, "jockey", $lrdate, $conn->defaults['past_days']);
    $topTrainersRecent=topTen($conn, "trainer", $lrdate, $conn->defaults['past_days']);
    echo "
    <div class='center'>
    <div style='float: left;' onmouseout=\"clearInfo()\">
    <table id='topTenLists'>
      <caption>Top 10 Wins Lists for {$conn->defaults['start_date']} thru $lrdate & Last {$conn->defaults['past_days']} Days</caption>
      <thead>
        <th>Trainer</th>
        <th>Meet</th>
        <th>Trainer</th>
        <th>Last {$conn->defaults['past_days']}</th>
        <th class='thick'>Jockey</th>
        <th>Meet</th>
        <th>Jockey</th>
        <th>Last {$conn->defaults['past_days']}</th>
      </thead>
      <tbody>
    ";

    for($row=0; $row<count($topJockeys); ++$row) {
      echo "
        <tr>
          <td onmouseover=\"showIndividualStats('trainer', '".$topTrainers[$row]['name']."')\">{$topTrainers[$row]['name']}</td>
          <td onmouseover=\"showGraphic('trainer', '".addslashes($topTrainers[$row]['name'])."')\"
              class='nums'>
              <div class='tooltip2'>{$topTrainers[$row]['wins']}
                <span class='tooltiptext2'>{$topTrainers[$row]['favs']} fav
                                       <br>{$topTrainers[$row]['turfs']} turf
                                       <br>$".round($topTrainers[$row]['avg_odds'],1)." odds
                </span>
              </div>
          </td>

          <td onmouseover=\"showIndividualStats('trainer', '".addslashes($topTrainersRecent[$row]['name'])."')\">{$topTrainersRecent[$row]['name']}</td>
          <td onmouseover=\"showGraphic('trainer', '".addslashes($topTrainersRecent[$row]['name'])."')\"
              class='nums'>
            <div class='tooltip2'>{$topTrainersRecent[$row]['wins']}
              <span class='tooltiptext2'>{$topTrainersRecent[$row]['favs']} fav
                                     <br>{$topTrainersRecent[$row]['turfs']} turf
                                     <br>$".round($topTrainersRecent[$row]['avg_odds'],1)." odds
              </span>
            </div>
          </td>

          <td class='thick' 
              onmouseover=\"showIndividualStats('jockey', '".addslashes($topJockeys[$row]['name'])."')\">{$topJockeys[$row]['name']}</td>
          <td 
              onmouseover=\"showGraphic('jockey', '".addslashes($topJockeys[$row]['name'])."')\"
              class='nums'>
            <div class='tooltip2'>{$topJockeys[$row]['wins']}
              <span class='tooltiptext2'>{$topJockeys[$row]['favs']} fav
                                     <br>{$topJockeys[$row]['turfs']} turf
                                     <br>$".round($topJockeys[$row]['avg_odds'],1)." odds
              </span>
            </div>
          </td>

          <td onmouseover=\"showIndividualStats('jockey', '".addslashes($topJockeysRecent[$row]['name'])."')\">{$topJockeysRecent[$row]['name']}</td>
          <td onmouseover=\"showGraphic('jockey', '".addslashes($topJockeysRecent[$row]['name'])."')\"
              class='nums'>
            <div class='tooltip2'>{$topJockeysRecent[$row]['wins']}
              <span class='tooltiptext2'>{$topJockeysRecent[$row]['favs']} fav
                                     <br>{$topJockeysRecent[$row]['turfs']} turf
                                     <br>$".round($topJockeysRecent[$row]['avg_odds'],1)." odds
              </span>
            </div>
          </td>
        </tr>
      ";
    }
    echo "
      </tbody>
    </table>
    <br>
    </div>
    <div id='individual_info' style='float: left; visibility:hidden;'></div>
    </div>
    ";
    
    // --- entry url
    $url="edit_winner.php?tb17_id";

    // -- get results for last date run
    $query = "SELECT
                tb17_id,
                race,
                race_date,
                distance,
                turf,
                race_class,
                sex,
                age,
                odds,
                horse,
                jockey,
                trainer,
                race_flow,
                comment,
                favorite
              FROM tb17
              WHERE race_date='$lrdate' AND {$conn->defaults['meet_filter']}
              ORDER BY race
            ";

    $stmt = $conn->db->prepare($query);  
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($tb17_id, 
                       $race,
                       $race_date,
                       $distance,
                       $turf,
                       $race_class,
                       $sex,
                       $age,
                       $odds,
                       $horse,
                       $jockey,
                       $trainer,
                       $race_flow,
                       $comment,
                       $favorite);

    // -- build html result table
    $date = new DateTime($lrdate, new DateTimeZone('America/New_York'));
    $chart_file="http://www.equibase.com/premium/chartEmb.cfm?track={$conn->defaults['track_id']}&raceDate=".$date->format("m/d/y")."&cy=USA&rn=1";
    echo "
      <div class='center'>
      <div style='clear: left; float: left;' onmouseout=\"clearInfo()\">
      <table id='resultTable' class='tablesorter'>
        <caption>Latest Racing Day Results (Date: $lrdate - ".$date->format('l')."  $stmt->num_rows races)  <a target='_blank' href='$chart_file'>Charts</a></caption>
      <thead>
        <tr>
          <th>Race</th>
          <th>Distance</th>
          <th>Class</th>
          <th>Horse</th>
          <th>Jockey</th>
          <th>Trainer</th>
        </tr>
      </thead>
      <tbody>
      ";

    //$type="'id'"; // this line probably should be removed
    while($stmt->fetch()) {
      echo "
        <tr onmouseover=\"showIndividualStats('race',$tb17_id)\">
          <td class='nums'><a href='$url=$tb17_id'>$race</a></td>
          <td class='nums ".($turf=='TRUE' ? 'turf' : '')."'>$distance</td>
          <td>$race_class <sup>".($sex=='female' ? 'f ' :'')."$age</sup></td>
          <td>$horse".($favorite=='TRUE' ? '<sup>*</sup>' : '')."</td>
          <td>$jockey</td>
          <td>$trainer</td>
         </tr>
      ";
    }
    echo "
        </tbody>
        </table>
        </div>
        <div id='race_info' style='float: left; visibility:hidden;'></div>
      </div>
      <script>
        $(document).ready(function() {
          $('#resultTable').tablesorter({widgets: ['zebra']});
          $('#deadheats').text('".($deadheat_cnt/2)." races were deadheats');
          $('#multiwinners').text('".(($horse_cnts['Turf']+$horse_cnts['Dirt'])-$horse_cnts['Total'])." horses won on dirt & turf');
          $('#scratches_url').attr('href','{$conn->defaults['scratches_url']}');
          $('#site_url').attr('href','{$conn->defaults['site_url']}');
          $('#title').text('{$conn->defaults['meet_name']}".(DB_PRODUCTION == 1 ? '' : ' ['.DB_NAME.']')."');
          // clear/hide dynamic eleemnts
          clearInfo();
        });
      </script>
    ";

    $stmt->free_result();
    $stmt->close();
    $conn->close();
?>
</body>
</html>