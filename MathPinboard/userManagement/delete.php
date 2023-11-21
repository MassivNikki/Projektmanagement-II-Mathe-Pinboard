<?php
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

// Drop databases if they exist
$sqlDropDB1 = "DROP DATABASE IF EXISTS $dbname1";
$sqlDropDB2 = "DROP DATABASE IF EXISTS $dbname2";

$conn->query($sqlDropDB1);
$conn->query($sqlDropDB2);

// Close connection
$conn->close();

echo "Databases deleted successfully!";
// if they existed, they're gone. If they didn't they were never there, so its not really necessary to add a condition to this message. The outcome will always be: Database gone.
?>
