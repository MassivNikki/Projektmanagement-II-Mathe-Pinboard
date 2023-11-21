<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname1 = "database1";
$dbname2 = "database2";

//creates two new databases and tables for the users and the login tokens.

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

// Create databases
$sqlCreateDB1 = "CREATE DATABASE $dbname1";
$sqlCreateDB2 = "CREATE DATABASE $dbname2";

$conn->query($sqlCreateDB1);
$conn->query($sqlCreateDB2);

// Select the newly created databases
$conn->select_db($dbname1);

// Create table for Database 1
$sqlCreateTable1 = "CREATE TABLE users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL
)";

$conn->query($sqlCreateTable1);

$hashedAdminPass = password_hash("admin", PASSWORD_DEFAULT);

// Using a prepared statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
$stmt->bind_param("ss", $username, $hashedAdminPass);

// Set the values for the parameters
$username = "admin";

$stmt->execute();


// Select the other database
$conn->select_db($dbname2);

// Create table for Database 2
// login token needs to be extra large
$sqlCreateTable2 = "CREATE TABLE user_sessions (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    role VARCHAR(20) NOT NULL,
    login_token VARCHAR(100) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($sqlCreateTable2);

// Close connection
$conn->close();

echo "Databases and tables rebuilt successfully!";
?>
