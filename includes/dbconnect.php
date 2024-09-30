<?php
// Database connection details

// General settings
$host       = "localhost";
$database   = "exp_3";
$user       = "root";
$password   = "";

$db = mysqli_connect($host, $user, $password, $database)
or die("Error: " . mysqli_connect_error());


// Check for connection errors
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
