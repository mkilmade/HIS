<?php
require_once('includes/envInit.inc.php');
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
    $params = "";
    $paramValues = [ ];
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
        $params = $params . ($params == "" ? "" : ", ") . "?";
        
        // check for resource and insert new jockey or trainer or horse if does not exist
        if ($field == 'jockey' || $field == 'trainer' || $field == 'horse') {
        	$className = ucfirst($field);
        	$resObj = new $className();
        	// + used when leading characters match (user override to force new resource)
        	if (substr($value, 0, 1) == "+") {
        		$value = substr($value, 1);
        		$post[$field] = $value;
        	}
        	$status = $resObj->addResource($value);
        	if ($status) {
        		$id_field = $field."_id";
        		$status = "(Added #{$resObj->$id_field})";
        	} elseif($status == "") {
        		$status = "";
        	} else {
        		$status = "(Insertion Failed: $status)";                
            }
        } else {
            $status = "";
        }
        $paramValues[] = $value;
        
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
              <td>Params</td>
              <td>$params</td>
            </tr>";
    echo "<tr>
              <td>Param Values</td>
              <td>" . json_encode($paramValues) . "</td>
            </tr>";
    echo "<tr>
              <td>SQL</td>
              <td>INSERT INTO 'tbd'.'tb17' ($fields) VALUES ($params)</td>
            </tr>";
    $tb17Obj = new TB17();
    $status = $tb17Obj->insert_entry($post);
    if ($status) {
    	$status_txt = "Success: Inserted row id = " . $tb17Obj->tb17_id;
    	$status_style = "";
    } else {
    	$status_txt = "Failed: " . $status;
    	$status_style = "style='color: #DC143C';";
    }

    echo "<tr>
              <td $status_style >Insertion Status</td>
              <td $status_style >$status_txt</td>
            </tr>
      ";

    if (!$status) {
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
?>
  </table>
</body>
</html>
