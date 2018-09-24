<?php
session_start();
require_once('includes/config.inc.php');
require_once('includes/connection.inc.php');;
$conn = new Connection();

// get the tb17_id parameter from URL
$tb17_id = $_GET["tb17_id"];

$query = "SELECT race as 'Race',
             track_condition as 'Condition',
             turf as 'Turf',
             horse as 'Horse',
             time_of_race as 'Time',
             IF(favorite='TRUE',CONCAT(CAST(odds as char),'<sup>*</sup>'),odds) as 'Odds',
             race_flow as 'Flow',
             post_position as 'Post',
             field_size as 'Field',
             jockey as 'Jockey',
             trainer as 'Trainer',
             comment as 'Comment'
          FROM tb17 
          WHERE tb17_id = ?";

$stmt = $conn->db->prepare($query);
$stmt->bind_param('s', $tb17_id);
$stmt->execute();

echo "<table style='border: 3px solid black; color: black; background-color: #F5F5DC;'>
         <caption style='text-align: center; font-weight: bold;'>Race Information</caption>";

foreach ($stmt->get_result()->fetch_assoc() as $field => $value) {
    echo "<tr>
            <td style='text-align:right; border-bottom: 1px dotted;'>$field: </td>
            <td style='text-align:left; font-weight: bold; border-bottom: 1px dotted;'>$value</td>
         </tr>
        ";
}

echo "</table>";
$stmt->close();
$conn->close();
?>