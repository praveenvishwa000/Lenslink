<?php

$host = 'localhost';
$db   = 'image_store';
$user = 'root';
// $pass = '';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
