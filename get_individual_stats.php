<?php
session_start();
require_once('includes/config.inc.php');
require_once('includes/connection.inc.php');;
$conn = new Connection();
// get the tb17_id parameter from URL
$type = $_GET["type"];
$name = $_GET["name"];

$query = "SELECT  
             COUNT(*) as 'Wins',
             SUM(IF(turf='FALSE',1,0)) as 'Dirt',
             SUM(IF(turf='TRUE',1,0)) as 'Turf',
             TRUNCATE(AVG(odds),1) as 'Odds',
             TRUNCATE(AVG(IF(favorite='TRUE',odds,NULL)),1) as 'Favs Odds',
             SUM(IF(distance<'8',1,0)) as 'Sprints',
             SUM(IF(distance<'8' and turf='FALSE',1,0)) as 'Dirt Sprints',
             SUM(IF(distance<'8' and turf='TRUE',1,0)) as 'Turf Sprints',
             SUM(IF(distance>='8',1,0)) as 'Routes',
             SUM(IF(distance>='8' and turf='FALSE',1,0)) as 'Dirt Routes',
             SUM(IF(distance>='8' and turf='TRUE',1,0)) as 'Turf Routes'
          FROM tb17 
          WHERE $type = ? AND {$conn->defaults['meet_filter']}
          GROUP BY $type";

$stmt = $conn->db->prepare($query);
$stmt->bind_param('s', $name);
$stmt->execute();
$result = $stmt->get_result();
$results = $result->fetch_assoc();

echo "<table style='border: 3px solid black; color: black;background-color: #F5F5DC;'>
        <caption style='text-align: center; font-weight: bold;'>'$name'</caption>";

foreach ($results as $field => $value) {
    echo "<tr>
                <td style='text-align:right;
                           border-bottom: 1px dotted;'>$field: </td>
                <td style='text-align:left;
                           font-weight: bold;
                           border-bottom: 1px dotted;'>$value</td>
              </tr>
             ";
}

echo "</table>";
$stmt->close();
$conn->close();
?>