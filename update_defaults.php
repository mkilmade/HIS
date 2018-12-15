<?php
session_start();
require_once('includes/config.inc.php');
require_once('includes/connection.inc.php');
$conn = new Connection();

header('Location: session.php?reset_session=1');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = $_POST;
    unset($post['submit']);
    $query = "SELECT *
              FROM current_defaults
              LIMIT 1
             ";
    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $entry = $stmt->get_result()->fetch_assoc();

    foreach ($post as $field => $value) {
        if ($field == 'current_defaults_id')
            $id = $value;
        if ($value == $entry[$field]) {
            unset($post[$field]);
            continue;
        } else {
            if ($field == 'race_meet_id')
                // don't use track conditions for newly selected race meet
                unset($_SESSION['dirt_track_condition']);
                unset($_SESSION['turf_track_condition']);
        } // race_meet_id check 'if'
    } // value check 'else'

    if ((count($post)) > 0) {
        $status = $conn->update_row($post, 'current_defaults', $id);
        if ($status != "Success") {
            // log warning
            trigger_error("Warning -> Update " . $status, E_USER_WARNING);
        } // $status if
    } // count if
} // REQUEST_METHOD if

$stmt->close();
$conn->close();
?>
