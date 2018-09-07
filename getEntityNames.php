<?php
    session_start();
    require_once('includes/config.inc.php');
    include_once('includes/connection.php');
    $conn = new Connection();
    
    $searchname = $_GET['name'];
    $tablename = $_GET['entity_name'];
    $id=$tablename."_id";

    // first list those matching 'shortcut' field
    $query = "SELECT $id, name, shortcut
              FROM $tablename
              WHERE shortcut LIKE \"$searchname%\"
              ORDER BY shortcut";

    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id,$name,$shortcut);

    $names=array();
    if ($stmt->num_rows>0) {
        while($stmt->fetch()) {
         $names[]=array(
                'label' => htmlentities($shortcut.' - '.$name),
                'value' => htmlentities($name) # change to $id when normalized
         );
        }
    }
    $stmt->free_result();
    $stmt->close();

    // add those matching 'name' field
    $query = "SELECT $id, name
              FROM $tablename
              WHERE name LIKE \"$searchname%\"
              ORDER BY name";

    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id,$name);

    if ($stmt->num_rows>0) {
        while($stmt->fetch()) {
            $names[]=array(
                'label' => htmlentities($name),
                'value' => htmlentities($name) # change to $id when normalized
            );
        }
    }
    $stmt->free_result();
    $stmt->close();

    $conn->close();

    echo json_encode($names);
?>