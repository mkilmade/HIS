<?php
$root = dirname(__DIR__);
set_include_path ( $root . '/classes' . PATH_SEPARATOR . $root . '/includes' . PATH_SEPARATOR . $root . '/secure' . PATH_SEPARATOR . get_include_path () );
spl_autoload_extensions ( '.class.php,.inc.php,.php' );
spl_autoload_register ();
unset($root);
