<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "gabaylakad_db";

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_error){
    die("Failed to connect to database: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>