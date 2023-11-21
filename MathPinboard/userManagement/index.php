<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management</title>
    <style>
        /* Style for the popup */
        #loginPopup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
    </style>
</head>
<body>
    <h1>Database Management</h1>

    <!-- Link to open the login popup -->
    <a href="#" onclick="loginModule.openLoginPopup()">Login</a>

    <!-- Link to the secondary page -->
    <a href="secondary_page.php">Go to Secondary Page</a>

    <!-- Link to the registration page -->
    <a href="register.php">Register</a>
    <div id="loginPopupContainer"></div>
    <script>
        const baseUrl = "/userManagement/";
    </script>

    <!-- Include your script file -->
    <script src="a/script.js"></script></body>
</html>
