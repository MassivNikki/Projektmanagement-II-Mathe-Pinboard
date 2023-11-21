<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database1";

// Establish a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to change user's password
function changePassword($username, $oldPassword, $newPassword, $conn) {
    // Retrieve the current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($currentPassword);
    $stmt->fetch();
    $stmt->close();

    // Verify old password
    if ($oldPassword === $currentPassword) {
        // Prepare and execute the update query
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $newPassword, $username);

        if ($stmt->execute()) {
            return "Password updated successfully!";
        } else {
            return "Error updating password: " . $stmt->error;
        }

        $stmt->close();
    } else {
        return "Old password is incorrect!";
    }
}
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oldPassword = $_POST["old_password"];
    $newPassword = $_POST["new_password"];
    $username = $_POST["username"];

    $result = changePassword($username, $oldPassword, $newPassword, $conn);
    echo $result;
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
</head>
<body>
    <h2>Change Password</h2>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="old_password">Old Password:</label>
        <input type="password" name="old_password" required>
        <br><br>
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>
        <input type="hidden" id="usernameInput" name="username" value="null">
        <br><br>
        <input type="submit" value="Change Password">
    </form>

    <div id="loginPopupContainer"></div>
    <script>
        const baseUrl = '';
    </script>
    
    <script src="login.js"></script>
    <script>
 loginModule.autoCheckToken().then(() => {
        document.getElementById("usernameInput").value = loginModule.getUsername();
    });
    
</script>
<button onclick="window.history.back()">Go Back</button>

</body>
</html>
