<?php # edit_defaults.php  # script mjk 4/28/18
//      form to updatte entry in the tbd.current_defaults table
  session_start();
  require_once('includes/config.inc.php');
  include_once('includes/connection.inc.php');
  $conn = new Connection();
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
  <br/>

  <form id="editForm" action="update_defaults.php" method="post">

  <fieldset style="width: 425px; background-color: Azure; margin:auto" >
    <legend style="text-align: center">Edit Defaults</legend>

   <p><label>ID: <input type="number" min="1" max="99999" id="current_defaults_id" name="current_defaults_id" value="" readonly></label></p>

   <p><label>Past Days: <input type="number" min="1" max="28" id="past_days" name="past_days"></label></p>

   <p><label>Meet: <select id="race_meet_id" style="width: 300px;"  name="race_meet_id" ></select></label></p>

   <p><label>Previous Track Default: <select id="previous_track_id" name="previous_track_id" style="width: 60px;">
              <option value="BEL">BEL</option>
              <option value="SAR">SAR</option>
              <option value="AQU">AQU</option>
              <option value="MTH">MTH</option>
              <option value="CD">CD</option>
              <option value="GP">GP</option>
              <option value="KEE">KEE</option>
              <option value="WO">WO</option>
              <option value="OP">OP</option>
              <option value="FG">FG</option>
              <option value="FL">FL</option>
              <option value="SA">SA</option>
              <option value="LA">LA</option>
              <option value="DMR">DMR</option>
              <option value="AP">AP</option>
              <option value="EIP">EIP</option>
              <option value="DEL">DEL</option>
              <option value="FL">FL</option>
              <option value="LRC">LRC</option>
              <option value="LRL">LRL</option>
              <option value="LBS">LBS</option>
              <option value="PIM">PIM</option>
              <option value="HOU">HOU</option>
              <option value="LS">LS</option>
              <option value="TAM">TAM</option>
              <option value="SUF">SUF</option>
              <option value="PRX">PRX</option>
              <option value="PID">PID</option>
              <option value="PEN">PEN</option>
              <option value="MED">MED</option>
              <option value="ML">ML</option>
              <option value="IND">IND</option>
              <option value="ASC">ASC</option>
       </select></label></p>

   </fieldset>

   <p style="text-align: center"><input type="submit" name="submit" value="Update"></p>

  </form>
 </body>
 <script>
 $(document).ready(function() {

 <?php
    $query = "SELECT cd.current_defaults_id,
                     cd.past_days,
                     cd.previous_track_id,
                     rm.race_meet_id
              FROM current_defaults AS cd
              INNER JOIN race_meet AS rm
                 USING (race_meet_id)
              LIMIT 1
             ";
    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $result=$stmt->get_result();
    $results=$result->fetch_assoc();
    // fill in form fields with database values";
    foreach($results as $field => $value) {
      if ($field=='race_meet_id') {
        $current_race_meet_id=$value;
        continue;
      }
      echo "
        $(\"#$field\").val(\"$value\");
        ";
    }
    $stmt->close();

    // set <option> tags for meet <select>
    $query = "SELECT race_meet_id,
                     name
              FROM race_meet
              ORDER BY start_date DESC
             ";
    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($race_meet_id, $name);
    while($stmt->fetch()) {
      $selected=$race_meet_id==$current_race_meet_id ? 'selected' : '';
      echo "$('#race_meet_id').append(\"<option value='$race_meet_id' $selected>".addslashes($name)."</option>\");";
    }
    $stmt->close();
    $conn->close();
?>

}); // finish .ready function
</script>
</html>

