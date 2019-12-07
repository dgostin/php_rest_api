<?php
include 'database.php';
include 'people.php';

$output_type = "json";
if (isset ($_GET["format"])) {
	if ($_GET["format"] == 'csv') {
		$output_type = 'csv';
	}
}

if ($output_type == 'csv') {
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=people.csv');
}
else {
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
}

$database = new Database();
$db = $database->getConnection();

// initialize object
$people = new People($db);

$where_clause = $people->get_where_clause_from_url();

$stmt = $people->get_data($where_clause);
$num = $stmt->rowCount();
 
if($num>0){
 
	$people=array();
 
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        extract($row);
 
        $data=array(
            "id" => $id,
            "first_name" => $first_name,
            "last_name" => $last_name, 
            "email" => $email, 
            "gender" => $gender, 
            "title" => $title, 
            "dept" => $dept
        );
 
        array_push($people, $data);
    }
 
    // set response code - 200 OK
    http_response_code(200);
 
    // show products data in proper format
    if ($output_type == 'csv') {
		$out = fopen('php://output', 'w');
    	foreach ($people as $data) {
	    	fputcsv($out, $data);
    	}
   		fclose($out);
    }
    else {
    	echo json_encode($people);
    }

}
else{
     
 	if ($output_type != 'csv') {
 		// set response code - 404 Not found
    	http_response_code(404);
    	echo json_encode(
        	array("message" => "No data found.")
    	);
    }
}

?>