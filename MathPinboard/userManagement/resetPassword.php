<?php
// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "database1";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Reset the user's password to "password" (modify the query as needed)
    $sqlResetPassword = "UPDATE users SET password='password' WHERE id=$userId";

    if ($conn->query($sqlResetPassword) === TRUE) {
        echo "User password reset successfully.";
    } else {
        echo "Error resetting user password: " . $conn->error;
    }

    $conn->close();
} else {
    echo "User ID not provided.";
}
?>
