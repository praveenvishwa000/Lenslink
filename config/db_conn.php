<<<<<<< HEAD
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
=======
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
>>>>>>> 644698dfc1ca2b7d65e44b7ba9e874a5fe15fc50
