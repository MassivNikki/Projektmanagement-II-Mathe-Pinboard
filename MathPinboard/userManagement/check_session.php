<?php
// Replace these values with your actual database connection details
$servername = "localhost";
$username = "root";
$password = "";
$databaseSessions = "database2";

// Retrieve the login token
$requestData = json_decode(file_get_contents("php://input"), true);
$loginToken = $requestData['loginToken'];

// Create connection to Sessions database
$connSessions = new mysqli($servername, $username, $password, $databaseSessions);

// Check connection to Sessions database
if ($connSessions->connect_error) {
    // Handle database connection error
    header('Content-Type: application/json');
    echo json_encode(['valid' => false, 'username' => null, 'role' => null]);
    exit();
}


//delete all duplicate userTokens
$sql = "DELETE u1 FROM user_sessions u1
            INNER JOIN user_sessions u2
            WHERE u1.username = u2.username
            AND u1.timestamp < u2.timestamp";
$connSessions->query($sql);

//delete older Session tokens
$sql = "DELETE FROM user_sessions WHERE Timestamp < NOW() - INTERVAL 30 MINUTE";
$connSessions->query($sql);

// Query to check if the login token exists in UserSessions (Timestamp check should be useless, but safe is safe)
$sql = "SELECT * FROM user_sessions WHERE login_token = '$loginToken' AND Timestamp > NOW() - INTERVAL 30 MINUTE";
$result = $connSessions->query($sql);

// Check if a row was found (indicating a valid session)
if ($result && $result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $role = $row['role'];

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode(['valid' => true, 'username' => $username, 'role' => $role]);
} else {
    // Return that the session is not valid
    header('Content-Type: application/json');
    echo json_encode(['valid' => false, 'username' => null, 'role' => null]);
}

// Close the database connection
$connSessions->close();
?>
