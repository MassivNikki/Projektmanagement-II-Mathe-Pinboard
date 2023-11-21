<?php
session_start();

$servername = "localhost";
$datausername = "root";
$datapassword = "";
$usersDatabase = "database1";
$sessionsDatabase = "database2";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $connUsers = new mysqli($servername, $datausername, $datapassword, $usersDatabase);

    // Check connection
    if ($connUsers->connect_error) {
        $response = array("success" => false, "message" => "Connection to Users database failed: " . $connUsers->connect_error);
    } else {
        $username = $_POST["username"];
        $unhashedPassword = $_POST["password"];

        $password = password_hash($unhashedPassword,PASSWORD_DEFAULT);
    

        $sqlUsers = "SELECT * FROM users WHERE username = '$username'";
        $resultUsers = $connUsers->query($sqlUsers);

        if ($resultUsers->num_rows == 1) {
            // Login successful
            $rowUsers = $resultUsers->fetch_assoc();
            $role = $rowUsers["role"];
            $userPassword = $rowUsers["password"];

            if(password_verify($unhashedPassword,$userPassword)){

            $_SESSION["username"] = $username;

            // Generate a unique login token
            $loginToken = bin2hex(random_bytes(32));

            // Set the cookie with the login token
            setcookie("login_token", $loginToken, time() + (60 * 30), "/"); // Cookie valid for 30 Minutes

            // Switch to the Sessions database
            $connSessions = new mysqli($servername, $datausername, $datapassword, $sessionsDatabase);
            
            // Check connection
            if ($connSessions->connect_error) {
                $response = array("success" => false, "message" => "Connection to Sessions database failed: " . $connSessions->connect_error);
            } else {
                //delete older Session tokens
                $deleteSql = "DELETE FROM user_sessions WHERE Timestamp < NOW() - INTERVAL 30 MINUTE";
                $connSessions->query($deleteSql);
                // Insert a new record into user_sessions table
                $timestamp = date('Y-m-d H:i:s');
                $insertSql = "INSERT INTO user_sessions (username, Role, login_token, Timestamp) VALUES ('$username', '$role', '$loginToken', '$timestamp')";
                $resultSessions = $connSessions->query($insertSql);

                if ($resultSessions) {
                    // Successfully inserted into UserSessions
                    $response = array("success" => true, "message" => "Login successful!");
                } else {
                    // Failed to insert into UserSessions
                    $response = array("success" => false, "message" => "Error during login process: " . $connSessions->error);
                }

                $connSessions->close();
            }
        } else {
            // Login failed
            $response = array("success" => false, "message" => "Invalid username or password");
        }
        } else {
            // Login failed
            $response = array("success" => false, "message" => "Invalid username or password");
        }

        $connUsers->close();
    }

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT); // Include JSON_PRETTY_PRINT for better readability
}
?>
