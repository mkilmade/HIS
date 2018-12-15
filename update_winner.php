<?php 
  session_start();
  spl_autoload_register(function ($class) {
  	require_once "classes/". $class . '.class.php';
  });
  require_once('includes/config.inc.php');
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
  <script src="jquery/jquery.js"></script>
  <script src="jquery/jquery.tablesorter.js"></script>
  <title>Updating Winner</title>
</head>
<body>
  <script>
    $(document).ready(function() 
      { 
          $("#vTable").tablesorter(); 
      } 
    );
  </script>

  <h1>Updating Winner...</h1>
  <table>
    <tr>
      <td><a href='index.php'>Home</a></td>
      <td><a href='add_winner.php'>Add Winner</a></td>
      <td><a href='browse.php'>Browse/Edit Winners</a></td>
    </tr>
  </table>
  <br/>
  <table id='vTable' border=1 style="border-spacing: 2px; border-padding: 2px;" class='tablesorter'>
    <thead>
      <tr>
        <th>Changed Field</th>
        <th>New Value</th>
        <th>Database Value</th>
      </tr>
    </thead>
<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $fldvals = "";
      $post = $_POST;
      unset($post['submit']);
      
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
          //clog("previous values has been unset!");
      }
	  // get current values for comparisons via object
      $current = new TB17($_POST['tb17_id']);
      
      foreach($post as $field => $value) {
        // no need to update field if same value
        if ($value == $current->$field) { //[$field]) {
          unset($post[$field]);
          continue;
        }
        
        // check for resource and insert new jockey or trainer or horse if does not exist
        if ($field == 'jockey' || $field == 'trainer' || $field == 'horse') {
        	$className = ucfirst($field);
        	$resObj = new $className();
        	$status = $resObj->addResource($value);
        	if ($status) {
        		$id_field = $field."_id";
        		$status = "(Added #{$resObj->$id_field})";
        	} elseif($status == "") {
        		$status = "";
        	} else {
        		$status = "(Insertion Failed: $status}";
        	}
        } else {
        	$status = "";
        }
        
        echo "<tr>
                <td>$field</td>
                <td>$value $status</td>
				<td>{$current->$field}</td>
              </tr>";
        $fldvals=$fldvals.($fldvals=="" ? "" : ", ").$field."='".addslashes($value)."'";
      }

      if ((count($post))==0){
        echo "<tr>
                <td>SQL</td>
                <td># of fields: ".(count($post))."</td>
                <td>Nothing to update!</td>
              </tr>";
      } else {
        echo "<tr>
                <td>SQL</td>
                <td># of fields: ".(count($post))."</td>
                <td>UPDATE tb17 SET $fldvals WHERE tb17_id='{$_POST['tb17_id']}'</td>
              </tr>";

        $status = $current->update_entry($post);
        if ($status) {
        	$status_txt = "Success";
        	$status_style = "";
        } else {
        	$status_txt = "Failed: " . $status;
        	$status_style = "style='color: #DC143C';";
        }
        
        echo "
          <tr>
            <td $status_style >Update Status</td>
            <td $status_style >$status_txt</td>
            <td/>
          </tr>
        ";

        if (!$status) {
          // log warning
          trigger_error("Warning -> Update ".$status, E_USER_WARNING);

          // make sure user knows there is an issue
          echo "
           <script>
              alert(\"".addslashes($status)."\");
            </script>
          ";
        } // $status if

      } // count() else
    } // REQUEST_METHOD if
?>
  </table>
</body>
</html>
