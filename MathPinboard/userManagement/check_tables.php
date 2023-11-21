<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname1 = "database1";
$dbname2 = "database2";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if databases exist
$database1_exists = databaseExists($conn, $dbname1);
$database2_exists = databaseExists($conn, $dbname2);

// Close connection
$conn->close();

// If databases don't exist, set session variable
if (!$database1_exists || !$database2_exists) {
    $_SESSION['databases_not_exist'] = true;
} else {
    $_SESSION['databases_not_exist'] = false;
}

function databaseExists($connection, $dbname) {
    $result = mysqli_query($connection, "SHOW DATABASES LIKE '$dbname'");
    return (mysqli_num_rows($result) > 0);
}
?>
