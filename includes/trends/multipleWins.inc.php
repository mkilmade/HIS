<?php

// called by getTrend.php
function multipleWins($conn)
{
    $query = "SELECT
                wins, horse
              FROM (
                    SELECT count(*) AS wins, horse
                    FROM tb17
                    WHERE {$conn->defaults['meet_filter']}
                    GROUP BY horse
                   ) AS multi_winners
              WHERE wins > '1'
              ORDER BY wins, horse";

    $stmt = $conn->db->prepare($query);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($wins, $horse);

    echo "
          <table id='multiWinsTable' class='tablesorter' style='width:200px; margin: auto; font-size:14px'>
            <caption>Multiple Wins at Meet ($stmt->num_rows)</caption>
            <thead>
              <th>Horse</th>
              <th>Wins</th>
            </thead>
           <tbody>
        ";

    while ($stmt->fetch()) {
        echo "<tr>";
        echo "<td style='text-align:left;'>$horse</td>";
        echo "<td>$wins</td>";
        echo "</tr>";
    }

    echo "
        </tbody></table>
        <script>
            $('#multiWinsTable').tablesorter({
              widgets: ['zebra']
            });
        </script>
        ";
    $stmt->free_result();
    $stmt->close();
} // function

?>