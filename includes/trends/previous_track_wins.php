<?php
    $total = 0;
    $query = "SELECT previous_track_id,
                     COUNT(*) as wins
              FROM tb17
              WHERE previous_track_id IS NOT NULL AND
                    {$conn->defaults['meet_filter']}
              GROUP By previous_track_id
              ORDER BY wins DESC, previous_track_id";
 
    $stmt = $conn->db->prepare($query);  
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($previous_track_id,
                       $wins);

    echo "
      <div style='overflow-x:auto;float: left;'>
      <table id='trackTable' class='tablesorter' style='width:200px; font-size:14px'>
        <caption>Previous Track Stats ($stmt->num_rows)</caption>
        <thead>
          <th>Track</th>
          <th>Wins</th>
        </thead>
    <tbody>
    ";
    $total = 0;
    while($stmt->fetch()) {
      echo "<tr>";
      echo "<td>$previous_track_id</td>";
      echo "<td>$wins</td>";
      echo "</tr>";
      $total += $wins;

    }
    $stmt->free_result();
    $stmt->close();

    // get FTS (first time starter) wins for meet
    $query = "SELECT COUNT(*)
              FROM tb17
              WHERE comment LIKE '%FTS%' and
                    {$conn->defaults['meet_filter']}
              LIMIT 1";
    $stmt = $conn->db->prepare($query);  
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($wins);

    while($stmt->fetch()) {
        echo "<tr>";
        echo "<td>Firsters</td>";
        echo "<td>$wins</td>";
        echo "</tr>";
        $total += $wins;
    }
    $stmt->free_result();
    $stmt->close();

    // add total row
    echo "<tr>";
    echo "<td><b>Total</b></td>";
    echo "<td><b>$total</b></td>";
    echo "</tr>";

    echo "</tbody></table></div>";

?>