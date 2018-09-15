<?php
  function clog($text)
  {
    if ($_SESSION['debug']=='off') return; 
    echo "
      <script>
        console.log('".addslashes($text)."');
      </script>
    ";
  }

  // not used; kept for reference
  function build_select($type)
  {
    include_once('connection.inc.php');
    $conn = new Connection();
    $html='';
    $varName="search".$type;
    $query = "SELECT name FROM ".$type. " ORDER BY name";
    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($name);
    while($stmt->fetch()) {
      $names[]=$name;
    }
    clog("Building select component for ".$type);

    $html=$html."<td><select name='".$varName."'>";
    $html=$html."<option value=''>All</option>";
    foreach($names as $name) {
      $html=$html."<option value='".$name."'>".$name."</option>";
    }
    $html=$html."</td></select>";
    $stmt->free_result();
    $stmt->close();
    $conn->close();
    return $html;
  }
  function dump_backtrace() {
      echo "<pre>".json_encode(debug_backtrace(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT)."</pre>";
      echo "<script>console.log(".json_encode(debug_backtrace(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT).");</script>";
      ob_flush();
      flush();
  }
?>