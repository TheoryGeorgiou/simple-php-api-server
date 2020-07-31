<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('UTC');

$servername = "localhost";
$username = "";
$password = "";
$database = "";

include('include/connect.php');	

//utility functions.
//response handling of api
$response = array();
function scr_respond(){
    global $response;
    echo json_encode($response,true);
    exit;
}

?>
