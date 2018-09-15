<?php
    session_start();
    require_once('includes/config.inc.php');
    include_once('includes/connection.inc.php');
    $conn = new Connection();
    
    $name = $_GET['name'];
    $category = $_GET['entity_name'];

    $query = "SELECT DISTINCT $category
              FROM tb17
              WHERE $category LIKE \"$name%\"
              ORDER BY $category";

    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($cat);

    $cats=array();
    while($stmt->fetch()) {
      $cats[]=array(
            'label' => htmlentities($cat),
            'value' => htmlentities($cat)
        );
    }

    $stmt->free_result();
    $stmt->close();
    $conn->close();

    echo json_encode($cats);
?>