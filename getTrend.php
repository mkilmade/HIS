<?php
    session_start();
    require_once('includes/config.inc.php');
    include_once('includes/connection.php');
    
    include_once('includes/trends/keyRaces.php');
    include_once('includes/trends/multipleWins.php');
    include_once('includes/trends/previouslyRanAtMeet.php');
    include_once('includes/trends/previousRaceAtMeetPerCard.php');
    
    // call trend function requested
    $conn = new Connection();
    $trend = $_GET['trend'];
    
    switch($trend) {
        case 'multipleWins':
            multipleWins($conn);
            break;
        case 'keyRaces':
            keyRaces($conn);
            break;
        case 'previouslyRanAtMeet':
            previouslyRanAtMeet($conn);
            break;
        case 'previousRaceAtMeetPerCard':
            previousRaceAtMeetPerCard($conn);
            break;
        default:
            break;
    }
    $conn->close();
?>