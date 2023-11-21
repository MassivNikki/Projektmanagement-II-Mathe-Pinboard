<?php
// Include your database connection file or establish a connection here
// Example: include 'db_connection.php';
// Ensure to handle errors and sanitize inputs to prevent SQL injection

// Assuming you have a connection named $conn
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "database1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to delete user account
function deleteUser($username, $password, $conn) {
    // Retrieve the current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($currentPassword);
    $stmt->fetch();
    $stmt->close();

    // Verify password
    if (password_verify($password, $currentPassword)) {
        // Prepare and execute the delete query
        $stmt = $conn->prepare("DELETE FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            return "Account deleted successfully!";
        } else {
            return "Error deleting account: " . $stmt->error;
        }

        $stmt->close();
    } else {
        return "Password is incorrect!";
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmation = $_POST["confirmation"];

    //Not the usual popup-confirmation, but it should work
    if ($confirmation === "yes") {

        $result = deleteUser($username, $password, $conn);
        echo $result;
    } else {
        echo "Account deletion canceled.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
</head>
<body>
    <h2>Delete Account</h2>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="password">Password:</label>
        <br>
        <input type="password" name="password" required>
        <br><br>
        <label for="confirmation">Are you sure you want to delete your account? Type 'yes' to confirm:</label>
        <input type="text" name="confirmation" required>
        <input type="hidden" id="usernameInput" name="username" value="null">
        <br><br>
        <input type="submit" value="Delete Account">
    </form>

    <div id="loginPopupContainer"></div>
    <script>
        //again, the whole login script stuff
        const baseUrl = '';
    </script>
    
    <script src="login.js"></script>
    <script>
        //i dunno why but the eventlistener is required else it wouldnt execute
        //this automatically fills in the username via cookie
        document.addEventListener('DOMContentLoaded', function() {
        loginModule.autoCheckToken().then(() => {
        document.getElementById("usernameInput").value = loginModule.getUsername();
        console.log(loginModule.getUsername());
    });
            console.log("Script executed on load");
        });
    </script>
        <button onclick="window.history.back()">Go Back</button>
        
</body>
</html>
