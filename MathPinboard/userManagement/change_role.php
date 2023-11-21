<?php
// Check if ID is provided in the URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Prevent changing the role for the admin user
    if ($userId == 1) {
        echo "Cannot change the role for the admin user.";
        exit;
    }

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

    // Update the user's role
    $newRole = "new_role_value"; // Replace with the desired new role
    $sqlUpdateRole = "UPDATE users SET role='$newRole' WHERE id=$userId";

    if ($conn->query($sqlUpdateRole) === TRUE) {
        echo "User role updated successfully.";
    } else {
        echo "Error updating user role: " . $conn->error;
    }

    $conn->close();
} else {
    echo "User ID not provided.";
}
?>
