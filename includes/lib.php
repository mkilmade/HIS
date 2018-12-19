<?php
spl_autoload_register(function ($class) {
	require_once 'classes/' . $class . '.class.php';
});
require_once('includes/config.inc.php');
		
function clog($text)
  {
    if ($_SESSION['debug']=='off') return; 
    echo "
      <script>
        console.log('".addslashes($text)."');
      </script>
    ";
  }

  function dump_backtrace() {
      echo "<pre>".json_encode(debug_backtrace(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT)."</pre>";
      echo "<script>console.log(".json_encode(debug_backtrace(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT).");</script>";
      ob_flush();
      flush();
  }
?>