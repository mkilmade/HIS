<?php
// edit_defaults.php # script mjk 4/28/18
// form to updatte entry in the tbd.current_defaults table
require_once ('includes/envInit.inc.php');
?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link href="jquery/jquery-ui.min.css" rel="stylesheet">
<script src="jquery/jquery.js"></script>
<script src="jquery/jquery.tablesorter.js"></script>
<script src="jquery/jquery-ui.min.js"></script>
<script src="js/common.js"></script>
<title>Edit Defaults</title>
</head>

<body>
	<table style="margin: auto;">
		<tr>
			<td><a href='index.php'>Home</a></td>
		</tr>
	</table>
	<br />

	<form id="editForm" action="update_defaults.php" method="post">

		<fieldset style="width: 425px; background-color: Azure; margin: auto">
			<legend style="text-align: center">Edit Defaults</legend>

			<p>
				<label>ID: <input type="number" min="1" max="99999"
					id="current_defaults_id" name="current_defaults_id" value=""
					readonly></label>
			</p>

			<p>
				<label>Past Days: <input type="number" min="1" max="28"
					id="past_days" name="past_days"></label>
			</p>

			<p>
				<label>Meet: <select id="race_meet_id" style="width: 300px;"
					name="race_meet_id"></select></label>
			</p>

			<p>
				<label>Previous Track Default: <input id="previous_track_id"
					name="previous_track_id" style="width: 60px;"></label>
			</p>

		</fieldset>

		<p style="text-align: center">
			<input type="submit" name="submit" value="Update">
		</p>

	</form>
</body>
<script>
 $(document).ready(function() {
	 acDomainFields('#previous_track_id');

<?php
$cdObj = Defaults::IdFactory(1);
// iterate through properties and set corresponding form fields
foreach ( $cdObj as $field => $value ) {
	echo "
        $(\"#$field\").val(\"$value\");
        ";
}

foreach ( Meet::getMeets () as $rmObj ) {
	$selected = $rmObj->race_meet_id == $cdObj->race_meet_id ? 'selected' : '';
	echo "$('#race_meet_id').append(\"<option value='{$rmObj->race_meet_id}' $selected>" . addslashes ( $rmObj->name ) . "</option>\");";
}
?>

}); // finish .ready function
</script>
</html>

