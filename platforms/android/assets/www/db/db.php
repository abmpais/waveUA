<?php
header("Access-Control-Allow-Origin: *");
$servername = "localhost";
$username = "root";
$password = "";
$db = "deca-wave";

// Create connection
$link = new mysqli($servername, $username, $password, $db);
// Check connection
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}
?>

