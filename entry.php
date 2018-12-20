<!DOCTYPE html>
<html>
<head>
<title>Quick Entry Dump</title>
</head>
<body>
	<h1>Quick Entry Dump</h1>
	<table>
		<tr>
			<td><a href='index.php'>Home</a></td>
			<td><a href='search.php'>Search</a></td>
		</tr>
	</table>
	<br />
	<table>	
<?php
require_once ('includes/envInit.inc.php');
$tb17Obj = new TB17 ( $_GET ['tb17_id'] );
// format html table rows
$html = "";
foreach ( $tb17Obj as $field => $value ) {
	$html .= "<tr><td align='right'>$field:</td><td><b>$value</b></td></tr>";
}
// send html to browser
echo $html;
?>
    </table>
</body>
</html>
