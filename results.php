<?php 
  session_start();
  require_once('includes/config.inc.php');
  include_once('includes/connection.inc.php');
  $conn = new Connection();
?>
<!DOCTYPE html>
<html>
<head>
  <script src="jquery/jquery.js"></script>
  <script src="jquery/jquery.tablesorter.js"></script>

  <title>Tampa Bay Downs 2017/18 Results</title>
</head>
<body>
  <script>
  $(document).ready(function() 
    { 
        $("#resultTable").tablesorter(); 
    } 
  );
  </script>
  <h1>Tampa Bay Downs 2017/18 Search Results</h1>
   <table>
    <tr>
      <td><a href='index.php'>Home</a></td>
      <td><a href='search.php'>Search</a></td>
    </tr>
  <br/>
  </table>
  <?php
    // create short variable names
    $searchorder=$_POST['searchorder'];
    if ($searchorder == '') {
      $searchorder='horse';
    }
    $searchtrainer=$_POST['searchtrainer'].'%';
    $searchjockey=$_POST['searchjockey'].'%';
    $searchhorse=$_POST['searchhorse'].'%';

    if (!$searchtrainer || !$searchjockey) {
       echo '<p>You have not entered search details.<br/>
       Please go back and try again.</p>';
       exit;
    }
    if (!$conn->db) {exit;}
    $query = "SELECT tb17_id, horse, jockey, trainer FROM tb17 WHERE trainer LIKE ? and jockey LIKE ? and horse LIKE ? order by $searchorder";
    $stmt = $conn->db->prepare($query);
    $stmt->bind_param('sss', $searchtrainer, $searchjockey, $searchhorse);  
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($tb17_id, $horse, $jockey, $trainer);

    echo "<p>Number of races found: ".$stmt->num_rows."</p>";
    echo "<table id='resultTable' border=1 cellspacing=2 cellpadding=2 class='tablesorter'>";
    echo "<thead><tr><th>id</th><th>Horse</th><th>Jockey</th><th>Trainer</th></tr></thead>";

    while($stmt->fetch()) {
      echo "<tr>";
      echo "<td><a href='entry.php?tb17_id=".$tb17_id."'>".$tb17_id."</a></td>";

      echo "<td>".$horse."</td>";
      echo "<td>".$jockey."</td>";
      echo "<td>".$trainer."</td>";
      echo "</tr>";
    }

    echo "</table>";

    $stmt->free_result();
    $stmt->close();
    $conn->close();
  ?>
</body>
</html>
