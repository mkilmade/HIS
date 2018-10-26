<?php # edit_winner.php?tb17_id=# script mjk 4/19/18
//      form to update winning entry in the tbd.tb17 table
  session_start();
  require_once('includes/config.inc.php');
  require_once('includes/connection.inc.php');
  $conn = new Connection();
?>
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
<title>Add Winner</title>
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

<script type="text/javascript">
function previous_trigger() {
    nextOutWinnersTable($("#previous_date").val(),
    		            $("#previous_race").val(),
    		            $("#previous_track_id").val());
}
</script>
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

  <?php require_once('includes/entry_input.inc.php'); ?>

  </fieldset>

   <p style="text-align: left"><input type="submit" name="submit" value="Update"></p>

  </form>
 </body>
 <script>
 $(document).ready(function() {
   setupCommonFields();
 <?php
    $query = "SELECT *
              FROM tb17
              WHERE tb17_id = ?
              LIMIT 1
             ";
    $stmt = $conn->db->prepare($query);
    $stmt->bind_param('s', $_GET['tb17_id']);  
    $stmt->execute();
    echo "
        // fill in form fields with database values";
    foreach($stmt->get_result()->fetch_assoc() as $field => $value) {
      if ($field=='favorite' || $field=='turf') {
        echo "
        $(\"input[name='$field'][value='$value']\").prop(\"checked\", true);";
        continue;
      }
      if ($field=='previous_track_id' && $value=='') {
        $value=$conn->defaults['previous_track_id'];
      }
      echo "
        $(\"#$field\").val(\"".addslashes($value)."\");";
    }
    $stmt->close();
    $conn->close();
?>

}); // finish .ready function
</script>
</html>

