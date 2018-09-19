<!DOCTYPE html>
<html>
<head>
<title>Tampa Bay Downs 2017/18 Entry</title>
</head>
<body>
	<h1>Raw Entry Result</h1>
	<table>
		<tr>
			<td><a href='index.php'>Home</a></td>
			<td><a href='search.php'>Search</a></td>
		</tr>
	</table>
	<br />
	<table>
	
<?php
    session_start();
    require_once('includes/config.inc.php');
    require_once('includes/connection.inc.php');;
    $conn = new Connection();
    
    // create short variable names
    $tb17_id = $_GET['tb17_id'];
    // $query = "SELECT tb17_id, race_date, horse, jockey, trainer, distance, turf, odds FROM tb17 WHERE tb17_id = ?";
    $query = "SELECT * FROM tb17 WHERE tb17_id = ?";
    $stmt = $conn->db->prepare($query);
    $stmt->bind_param('s', $tb17_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $results = $result->fetch_assoc();
    echo "";
    foreach ($results as $field => $value) {
        echo "<tr><td align='right'>$field</td><td>$value</td></tr>";
    }
    $stmt->free_result();
    $stmt->close();
    $conn->close();
?>
    </table>
</body>
</html>
