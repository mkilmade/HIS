<?php

// called by getTrend.php
function previousFinishTally($conn)
{
    $query = "SELECT 
                 COUNT(DISTINCT race_date,race),
                 previous_finish_position
              FROM tb17
              WHERE {$conn->defaults['meet_filter']} AND 
                    previous_track_id = '{$conn->defaults['track_id']}' AND 
                    previous_date >= '{$conn->defaults['start_date']}' AND 
                    previous_date <= '{$conn->defaults['end_date']}'
              GROUP BY previous_finish_position";

    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($count, $previous_finish_position);

    echo "
      <table id='previousFinishTable' class='tablesorter' style='width:225px; margin: auto; font-size:14px'>
        <caption>Previous Race At Meet</caption>
        <thead>
          <th>Prev Finish</th>
          <th>Next Out Win</th>
        </thead>
    <tbody>
    ";
    $total = 0;
    while ($stmt->fetch()) {
        $total += $count;
        echo "<tr>";
        echo "<td>$previous_finish_position</td>";
        echo "<td>$count</td>";
        echo "</tr>";
    }
    echo "
        <tr><td>Total</td><td>$total</td></tr>
        </tbody></table>

        <script>
            $('#previousFinishTable').tablesorter({
                widgets: ['zebra'],
                headers: {
                    0: {
                        sorter: false
                    },
                    1: {
                        sorter: false
                    }
                }
          });
          ";

    $stmt->free_result();
    $stmt->close();
} // function

?>