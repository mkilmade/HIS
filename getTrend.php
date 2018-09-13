<?php
    session_start();
    require_once('includes/config.inc.php');
    include_once('includes/connection.php');
    
    include_once('includes/trends/keyRaces.php');
    include_once('includes/trends/multipleWins.php');
    include_once('includes/trends/previouslyRanAtMeet.php');
    include_once('includes/trends/previousRaceAtMeetPerCard.php');
    
    // call trend function requested
    $_GET['trend']($conn);
?>