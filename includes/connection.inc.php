<?php
// not used but kept for now for refereance
require_once ('lib.php');
spl_autoload_register(function ($class) {
	require_once 'classes/' . $class . '.class.php';
});
	
class Connectionx
{

    // datebase connection.inc object used for queries/inserts/etc
    public $db;

    public $defaults;
	/*
	 * @name main constructor
	 */
    function __construct()
    {
        // get databace connection.inc
        include (MYSQL);
        $this->db = $db;
        $this->defaults = Defaults::get_his_defaults();
    }

    // close database connection.inc object
    public function close()
    {
        $this->db->close();
        // clog('Connection to database '.DB_NAME.' has been closed!');
    }
    
    public function execute_query($query)
    {
        $stmt = $this->db->stmt_init();
        if ($stmt->prepare($query)) {
            $status = $stmt->execute();
            if (! $status) {
                $status = $stmt->error;
                clog('Execute error: ' . $status);
            }
        } else {
            $status = $stmt->error;
            clog('Prepare error: ' . $status);
        }
        $stmt->close();
        clog('final status: ' . $status);
        return $status;
    }
    
    // insert a new row into a table
    public function insert_row(&$data, $table)
    {
        $fields = "";
        $values = "";
        foreach ($data as $field => $value) {
            $value = $this->db->escape_string(trim($value));
            $fields = $fields . ($fields == "" ? "" : ", ") . $field;
            $values = $values . ($values == "" ? "" : ", ") . "'" . $value . "'";
        }
        //echo "<br>INSERT INTO $table ($fields) VALUES ($values)";
        return $this->execute_query("INSERT INTO $table ($fields) VALUES ($values)");
    }

    // update an entry in a table
    public function update_row(&$data, $table, $id)
    {
        $fldvals = "";
        foreach ($data as $field => $value) {
            $value = $this->db->escape_string(trim($value));
            $fldvals = $fldvals . ($fldvals == "" ? "" : ", ") . $field . "='" . $value . "'";
        }
        return $this->execute_query("UPDATE $table SET $fldvals WHERE " . $table . "_id='" . $id . "'");
    }

    // -- get last race date for current meet
    public function last_race_date()
    {
        $query = "SELECT MAX(race_date)
              FROM tb17
              WHERE {$this->defaults['meet_filter']}
              LIMIT 1
             ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            return '';
        }
        $stmt->bind_result($last_race_date);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
        return $last_race_date;
    }

    // -- get number of race dates, so far for current meet
    public function getRaceDates()
    {
        $race_dates = array();
        $query = "SELECT DISTINCT race_date
              FROM tb17
              WHERE {$this->defaults['meet_filter']}
              ORDER BY race_date";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            return $race_dates;
        }
        $stmt->bind_result($race_date);
        $day_of_meet = 0;
        while ($stmt->fetch()) {
            $day_of_meet = $day_of_meet + 1;
            $race_dates[$race_date] = $day_of_meet;
        }
        $stmt->free_result();
        $stmt->close();
        return $race_dates;
    }

    // -- get win count by date for indiviual for current meet
    public function getWinCounts($type, $name)
    {
        $win_counts = array();
        $query = "SELECT DISTINCT race_date, 
                     COUNT(*) as win_count
              FROM tb17
              WHERE $type = ? AND {$this->defaults['meet_filter']}
              GROUP BY race_date
              ORDER BY race_date";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            return $win_counts;
        }
        $stmt->bind_result($race_date, $win_count);
        while ($stmt->fetch()) {
            $win_counts[$race_date] = $win_count;
        }
        $stmt->free_result();
        $stmt->close();
        return $win_counts;
    }

    // -- get last race # for a date for current meet (null) or for a specific date during meet
    public function last_race($race_date = null)
    {
        // -- get default value if $race_date is null
        if ($race_date === null) {
            $race_date = $this->last_race_date();
        }

        $query = "SELECT MAX(race)
                  FROM tb17
                  WHERE race_date = ? AND {$this->defaults['meet_filter']}
                  LIMIT 1
                 ";

        $stmt = $this->db->prepare($query);
        $stmt->bind_param('s', $race_date);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 0) {
            return 0;
        }
        $stmt->bind_result($last_race);
        $stmt->fetch();
        $stmt->free_result();
        $stmt->close();
        return $last_race;
    }

    public function addResource($tableName, $resourceName)
    {
        // query table for resource already exists in table
        $stmt = $this->db->prepare("SELECT * FROM $tableName WHERE name = ?");
        $stmt->bind_param('s', $resourceName);
        $stmt->execute();
        $stmt->store_result();
        // if resource is not already in table, insert entry
        if ($stmt->num_rows == 0) {
            $data = [
                "name" => $resourceName
            ];
            $className = ucfirst($tableName);
            $resObj = new $className();
            $status = $resObj->insert_entry($data);
            //$status = $this->insert_row($data, $tableName);
        } else {
            $status="";
        }
        $stmt->close();
        return $status;
    }

    // functions are nolonger used for retained for reference purpose
    // no longer used; kept for reference
    public function class_extent($tablename)
    {
        $query = "SELECT name 
                  FROM $tablename
                  ORDER BY name";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($name);
        $names = array();
        while ($stmt->fetch()) {
            $names[] = htmlentities($name);
        }
        $stmt->free_result();
        $stmt->close();
        return json_encode($names);
    }

    // no longer used; kept for reference
    public function distinct_category($category)
    {
        $query = "SELECT DISTINCT $category
                  FROM tb17
                  ORDER BY $category";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($cat);
        $cats = array();
        while ($stmt->fetch()) {
            $cats[] = htmlentities($cat);
        }
        $stmt->free_result();
        $stmt->close();
        return json_encode($cats);
    }
}
?>