<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<body>
    <h1>User Registration</h1>

    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="Register">
    </form>
    <button onclick="goBack()">Go Back</button>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>


<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname1 = "database1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname1);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST["username"];
    $unhashedPassword = $_POST["password"];
    $password = password_hash($unhashedPassword, PASSWORD_DEFAULT);
    $role = "user"; // Automatically set the role to "user"

    // Check if the username already exists
    $sqlCheckUsername = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sqlCheckUsername);

    if ($result->num_rows > 0) {
        echo "Error: Username already exists. Please choose a different username.";
    } else {
        // Insert data into the users table
        $sqlInsertUser = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";

        if ($conn->query($sqlInsertUser) === TRUE) {
            echo "User registered successfully!";
        } else {
            echo "Error: " . $sqlInsertUser . "<br>" . $conn->error;
        }
    }
}

// Close connection
$conn->close();
?>

