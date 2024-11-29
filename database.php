<?php

$host = "sql211.infinityfree.com";
$dbname = "if0_37812789_db_websys";
$username = "if0_37812789";
$password = "brQ67Habqh2s";

$mysqli = new mysqli($host,$username,$password,$dbname);

if ($mysqli -> connect_errno){
    die("Connection error: " . $mysqli -> connect_error);
}

return $mysqli;