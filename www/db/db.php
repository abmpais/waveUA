<?php
header("Access-Control-Allow-Origin: *");
$servername = "mysql-sa.mgmt.ua.pt";
$username = "deca-wave-dbo";
$password = "tMBNA9C6sex2LYCD";
$db = "deca-wave";

// Create connection
$link = new mysqli($servername, $username, $password, $db);
// Check connection


if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}
?>
