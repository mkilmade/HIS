<?php
  session_start();
  require_once('includes/config.inc.php');
  include_once('includes/connection.inc.php');
  $conn = new Connection();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Tampa Bay Downs 2017/18 Entry</title>
</head>
<body>
  <h1>Tampa Bay Downs 2017/18 Result</h1>
  <table>
    <tr>
      <td><a href='index.php'>Home</a></td>
      <td><a href='search.php'>Search</a></td>
    </tr>
  </table>
  <br/>
  <?php
    // create short variable names
    $tb17_id=$_GET['tb17_id'];
    //$query = "SELECT tb17_id, race_date, horse, jockey, trainer, distance, turf, odds FROM tb17 WHERE tb17_id = ?";
    $query = "SELECT * FROM tb17 WHERE tb17_id = ?";
    $stmt = $conn->db->prepare($query);
    $stmt->bind_param('s', $tb17_id);  
    $stmt->execute();
    $result=$stmt->get_result();
    echo "<table border=0>";
/*    while ($row = $result->fetch_assoc()) {
      echo "<tr><td align='right'>ID:</td><td>".$row['tb17_id']."</td></tr>";
      echo "<tr><td align='right'>Date:</td><td>".$row['race_date']."</td></tr>";
      echo "<tr><td align='right'>Distance:</td><td>".$row['distance']."</td></tr>";
      echo "<tr><td align='right'>Turf:</td><td>".$row['turf']."</td><td/></tr>";
      echo "<tr><td align='right'>Horse:</td><td>".$row['horse']."</td></tr>";
      echo "<tr><td align='right'>Jockey:</td><td>".$row['jockey']."</td></tr>";
      echo "<tr><td align='right'>Trainer:</td><td>".$row['trainer']."</td></tr>";
    }
*/   
     $results=$result->fetch_assoc();
     foreach($results as $field => $value) {
      echo "<tr><td align='right'>$field</td><td>$value</td></tr>";
        }

/*    $stmt->store_result();
    $stmt->bind_result($tb17_id, $race_date, $horse, $jockey, $trainer, $distance, $turf, $odds);

    echo "<table border=0>";
    //echo "<tr><td>id</<td><td>Date</td><td>Horse</td><td>Jockey</td><td>Trainer</td><td>Distance</td><td>Turf</td><td>Odds</td></tr>";

    while($stmt->fetch()) {
      echo "<tr><td align='right'>ID:</td><td>".$tb17_id."</td></tr>";
      echo "<td align='right'>Date:</td><td>".$race_date."</td></tr>";
      echo "<td align='right'>Distance:</td><td>".$distance."</td></tr>";
      echo "<td align='right'>Turf:</td><td>".$turf."</td><td/>";
      echo "<tr><td align='right'>Horse:</td><td>".$horse."</td></tr>";
      echo "<tr><td align='right'>Jockey:</td><td>".$jockey."</td></tr>";
      echo "<tr><td align='right'>Trainer:</td><td>".$trainer."</td></tr>";
      echo "<tr><td align='right'>Odds:</td><td>".$odds."</td></tr>";
    }
*/
    echo "</table>";

    $stmt->free_result();
    $stmt->close();
    $conn->close();
  ?>
</body>
</html>
