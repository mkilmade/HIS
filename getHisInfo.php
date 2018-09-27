<?php
    session_start();
    require_once('includes/config.inc.php');
    require_once('includes/connection.inc.php');;
    $conn = new Connection();
    
    switch($_GET['type']) {
        case('next_race'):
            echo getNextRaceNumber($_GET['race_date'], $conn);
            break;
        default:
            echo 'Invalid request';
    }
    $conn->close();
    
    function getNextRaceNumber($race_date, &$conn) {
        return json_encode(array('next_race' => $conn->last_race($race_date) + 1));
    }
?>