<?php # edit_winner.php?tb17_id=# script mjk 4/19/18
//      form to update winning entry in the tbd.tb17 table
require_once('session.php');?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<link href="jquery/jquery-ui.min.css" rel="stylesheet">
<link type="text/css"
	href="themes/green/style.css?v=<?php echo filemtime('themes/green/style.css'); ?>"
	rel="stylesheet">
<script src="jquery/jquery.js"></script>
<script src="jquery/jquery.tablesorter.js"></script>
<script src="jquery/jquery.tablesorter.pager.js"></script>
<script src="jquery/jquery-ui.min.js"></script>
<script src="js/common.js"></script>
<title>Edit Winner</title>
<style>
.turf {
	background-color: #32CD32;
}

.dirt {
	background-color: #F5F5DC;
}

h2 {
	text-align: center;
}

table#nowTable {
	border: 1px;
	border-collapse: separate;
	border-spacing: 2px;
	text-align: center;
}

table#nowTable td {
	padding: 0px;
}
</style>
</head>

<body>
  <table>
    <tr>
      <td><a href='index.php'>Home</a></td>
      <td><a href='add_winner.php'>Add Winner</a></td>
      <td><a href='browse.php'>Browse</a></td>
    </tr>
  </table>
  <br/>

  <form id="editForm" action="update_winner.php" method="post">

  <fieldset style="width: 425px; background-color: Azure" >
    <legend style="text-align: center">Edit Entry</legend>

  <p><label>ID: <input type="number" min="1" max="99999" id="tb17_id" name="tb17_id" value="" readonly></label></p>

  <?php require_once('includes/entry_input.inc.html'); ?>

  </fieldset>

   <p style="text-align: left"><input type="submit" name="submit" value="Update"></p>

  </form>
 </body>
 <script>
 $(document).ready(function() {
   setupCommonFields();
 <?php
    $winnerObj = new TB17($_GET['tb17_id']);
    echo "
    // fill in form fields with winner object values";
    foreach($winnerObj as $field => $value) {
    	if ($field=='favorite' || $field=='turf') {
        echo "
        $(\"input[name='$field'][value='$value']\").prop(\"checked\", true);";
        continue;
      }
      echo "
        $(\"#$field\").val(\"".addslashes($value)."\");";
    }
?>
  previous_trigger();
}); // finish .ready function
</script>
</html>

