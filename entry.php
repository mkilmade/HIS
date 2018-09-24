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
    
    // each entry field/value pairs
    $stmt = $conn->db->prepare("SELECT * FROM tb17 WHERE tb17_id = ?");
    $stmt->bind_param('s', $_GET['tb17_id']);
    $stmt->execute();
    
    // format html
    $html="";
    foreach ($stmt->get_result()->fetch_assoc() as $field => $value) {
        $html .= "<tr><td align='right'>$field:</td><td><b>$value</b></td></tr>";
    }
    $stmt->free_result();
    $stmt->close();
    $conn->close();
    
    // send html to browser
    echo $html;
?>
    </table>
</body>
</html>
