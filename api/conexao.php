<?php

$servername = '127.0.0.1';
$username = 'root';
$password = '';
$dbname = 'db_livroowl';

// Create connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

?>