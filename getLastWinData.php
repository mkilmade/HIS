<?php
// URL called by common.js [function: horse_trigger()]
session_start();
require_once('includes/config.inc.php');
require_once('includes/connection.inc.php');;
$conn = new Connection();
// get the horse parameter from URL
$horse = $_GET["horse"];
$query = "SELECT trainer, jockey
           FROM tb17 
          WHERE horse = ?
       ORDER BY race_date DESC
          LIMIT 1";

$stmt = $conn->db->prepare($query);
$stmt->bind_param('s', $horse);
$stmt->execute();
$result = $stmt->get_result();
$lastWinData = $result->fetch_assoc();
if (count($lastWinData) == 0) {
    $lastWinData["trainer"] = "";
    $lastWinData["jockey"] = "";
}
echo json_encode($lastWinData);
$stmt->close();
$conn->close();
?>