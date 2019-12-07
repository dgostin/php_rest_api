<?php

class People {
 
    // database connection and table name
    private $conn;
    private $table_name = "people";
    
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    function get_data($where_clause){
 
    	$query = "
    	SELECT id, first_name, last_name, email, gender, title, dept
    	FROM   people
    	$where_clause
    	";

	 	$stmt = $this->conn->prepare($query);
 		$stmt->execute();
 
    	return $stmt;

    }


    function get_where_clause_from_url () {

    	// Sanitize data and create where clause

    	$url_params = array();
    	$where = array();
    	$where_clause = '';

    	$url = $_SERVER['REQUEST_URI'];
		$url_arr = parse_url($url);
		if (!array_key_exists("query", $url_arr)) {
			return '';
		}
		
		parse_str($url_arr["query"], $url_params);
		

		if (array_key_exists('id', $url_params)) {
			if (is_numeric($url_params['id'])) {
				$where[] = 'id = ' . $url_params['id'];
			}
		}

		if (array_key_exists('first_name', $url_params)) {
			if (ctype_alpha($url_params['first_name'])) {
				$where[] = 'first_name = ' . "'" . $url_params['first_name'] . "'";
			}
		}
		
		if (array_key_exists('last_name', $url_params)) {
			if (ctype_alpha($url_params['last_name'])) {
				$where[] = 'last_name = ' . "'" . $url_params['last_name'] . "'";
			}
		}
		
		if (array_key_exists('email', $url_params)) {
			if (filter_var($url_params['email'], FILTER_VALIDATE_EMAIL)) {
				$where[] = 'email = ' . "'" . $url_params['email'] . "'";
			}
		}

		if (array_key_exists('gender', $url_params)) {
			if (ctype_alpha($url_params['gender'])) {
				$where[] = 'gender = ' . "'" . $url_params['gender'] . "'";
			}
		}

		if (array_key_exists('title', $url_params)) {
			if (preg_match("/\\w|\\s+/", $url_params['title'])) {
				$where[] = 'title = ' . "'" . $url_params['title'] . "'";
			}
		}

		if (array_key_exists('dept', $url_params)) {
			if (preg_match("/\\w|\\s+/", $url_params['dept'])) {
				$where[] = 'dept = ' . "'" . $url_params['dept'] . "'";
			}
		}

		if (!empty($where)) {
			$where_clause = "WHERE " . implode(" AND ", $where);
		}

		return $where_clause;

    }

}

?>