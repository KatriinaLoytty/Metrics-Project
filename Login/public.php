<?php
/*
Mohammad Jafarzadeh Rezvan
Metrics Monitoring Tool
Project Work 2014/2015
Updated: 3.8.2015
Here are the functions used for getting data from
the database for public pages.

*/

include('db_connection.php');

// For individual public projects
if(isset($_GET["id"]))
{
    // Create connection
    $con = mysqli_connect($hostname, $usrname, $password, $db_name);

    // Check connection
    if (mysqli_connect_errno($con)) {
        die("Connection failed: " . $conn -> connect_error);
    }

    $id = $_GET["id"];

    // Query
    $query = "SELECT * FROM `project` WHERE (`project_id` = '$id');";
    $result_project = mysqli_query($con, $query);
    $row_project = mysqli_fetch_array($result_project);
	$project = array(
	    "project_id" => $row_project['project_id'],
	    "project_name" => $row_project['project_name'],
	    "created_on" => $row_project['created_on'],
	    "updated_on" => $row_project['updated_on'],
	    "status" => $row_project['status'],
	    "version" => $row_project['version'],
	    "description" => $row_project['discription']
	);

    $project["working_hours"] = 0;

    $query = "SELECT `hours` FROM `individual_work` WHERE (`project_id` = '$id');";
    $result_project = mysqli_query($con, $query);
    while ($row_project = mysqli_fetch_array($result_project)) {
	       $project["working_hours"] += $row_project['hours'];
    }

    echo json_encode($project);
    mysqli_close($con);
}

// For all public projects
else if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    // Create connection
    $con = mysqli_connect($hostname, $usrname, $password, $db_name);

    // Check connection
    if (mysqli_connect_errno($con)) {
        die("Connection failed: " . $conn -> connect_error);
    }

    // Query
    $query = "SELECT * FROM `project` WHERE (`publicity` = '1');";
    $result_project = mysqli_query($con, $query);
    $count = 0;
    while ($row_project = mysqli_fetch_array($result_project))
    {
        $project = array(
            "project_id" => $row_project['project_id'],
            "project_name" => $row_project['project_name'],
            "created_on" => $row_project['created_on'],
            "updated_on" => $row_project['updated_on'],
            "status" => $row_project['status'],
            "version" => $row_project['version'],
            "description" => $row_project['discription']
        );
        $project["working_hours"] = 0;

        $project_table[$count] = $project;
        $count += 1;
    }

    for ($i=0; $i < $count; $i++) {
        $query = "SELECT `hours` FROM `individual_work` WHERE (`project_id` = '" . $project_table[$i]['project_id'] . "');";
        $result_project = mysqli_query($con, $query);
        while ($row_project = mysqli_fetch_array($result_project)) {
            $project_table[$i]['working_hours'] += $row_project['hours'];
        }
    }

    echo json_encode($project_table);
    mysqli_close($con);
}
?>
