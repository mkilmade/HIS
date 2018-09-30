<?php
session_start();
require_once ('includes/config.inc.php');
require_once ('includes/connection.inc.php');
$conn = new Connection();
?>
<!DOCTYPE html>
<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
<script src="jquery/jquery.js"></script>
<script src="jquery/jquery.tablesorter.js"></script>
<title>Add Winner Debug</title>
</head>
<body>
	<script>
    $(document).ready(function() 
      { 
          $("#vTable").tablesorter(); 
      } 
    );
  </script>

	<h1>Add Winner Review</h1>
	<table>
		<tr>
			<td><a href='index.php'>Home</a></td>
			<td><a href='add_winner.php'>Add Winner</a></td>
			<td><a href='browse.php'>Browse</a></td>
		</tr>
	</table>
	<br />
	<table id='vTable' border=1
		style="border-spacing: 2px; border-padding: 2px;" class='tablesorter'>
		<thead>
			<tr>
				<th>Field</th>
				<th>Value</th>
			</tr>
		</thead>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fields = "";
    $values = "";
    $post = $_POST;
    unset($post['submit']);
    unset($post['tb17_id']);

    // update $_SESSION with current race date conditions for automatic setting of last known condition
    // works best when adding winning entries by race number (lower to higher)
    if ($post['turf'] == 'TRUE') {
        $_SESSION['turf_track_condition'] = $post['track_condition'];
    } else {
        $_SESSION['dirt_track_condition'] = $post['track_condition'];
    }

    // needed for FTS entries
    if ($post['previous_date'] == '') {
        unset($post['previous_date']);
        unset($post['previous_race']);
        unset($post['previous_track_id']);
        unset($post['previous_finish_position']);
    }
    
    foreach ($post as $field => $value) {
        $fields = $fields . ($fields == "" ? "" : ", ") . $field;
        $values = $values . ($values == "" ? "" : ", ") . "'" . $value . "'";

        // check for resource and insert new jockey or trainer or horse if does not exist
        if ($field == 'jockey' || $field == 'trainer' || $field == 'horse') {
            $status = $conn->addResource($field, $value);
            switch($status) {
                case(1):
                    $status = "(Added)";
                    break;
                case(""):
                    break;
                default:
                    $status = "(Insertion Failed: $status";                
            }
        } else {
            $status = "";
        }
        
        echo "<tr>
                <td>$field</td>
                <td>$value $status</td>
              </tr>";
        unset($status);
    } // foreach
    
    echo "<tr>
              <td>Fields List</td>
              <td>$fields</td>
            </tr>";
    echo "<tr>
              <td>Values</td>
              <td>$values</td>
            </tr>";
    echo "<tr>
              <td>SQL</td>
              <td>INSERT INTO 'tbd'.'tb17' ($fields) VALUES ($values)</td>
            </tr>";

    $status = $conn->insert_row($post, "tb17");
    $status = ($status == 1) ? "Success" : "Failed: " . $status;
    $status_style = ($status == "Success") ? "" : "style='color: #DC143C';";

    echo "<tr>
              <td $status_style >Insertion Status</td>
              <td $status_style >$status $wins</td>
            </tr>
      ";

    if ($status != "Success") {
        // log warning
        trigger_error("Warning -> Insert " . $status, E_USER_WARNING);

        // make sure user knows there is an issue
        echo "
          <script>
            alert(\"" . addslashes($status) . "\");
          </script>
        ";
    } // if
}
$conn->close();
?>
  </table>
</body>
</html>
