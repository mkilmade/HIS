<?php
session_start();
require_once('includes/config.inc.php');
require_once('includes/connection.inc.php');;

require_once('includes/trends/keyRaces.inc.php');
require_once('includes/trends/multipleWins.inc.php');
require_once('includes/trends/previouslyRanAtMeet.inc.php');
require_once('includes/trends/previousRaceAtMeetPerCard.inc.php');
require_once('includes/trends/classTally.inc.php');
require_once('includes/trends/previousTrackWins.inc.php');
require_once('includes/trends/previousFinishTally.inc.php');

// call trend function requested
$conn = new Connection();
$_GET['trend']($conn);
$conn->close();

?>