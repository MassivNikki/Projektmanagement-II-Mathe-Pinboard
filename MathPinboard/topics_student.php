<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="topics.css">
    <script type="text/javascript" src="functionsJS_student.js"></script>
    <title>Themenauswahl</title>
</head>

<body>
<button class="backButton" title="Back to start" onclick="location.href='startseite_student.html';">Back</button>
<input class="searchBar" type="text" id="searchTopic" onkeyup="searchTopic()" placeholder="Search for Topics">
<input class="searchBar" type="text" id="searchTag" onkeyup="searchTag()" placeholder="Search for Tags"><br><br>
<?php
require './functions.php';
$dir = './topics';//in this folder are all the topics and post site files

$count = iterator_count(new FilesystemIterator($dir));
echo "<table ><tbody class='mainTable'></tbody></table>";
//print_r($_POST);

echo "<script>";
echo drawTableRows($dir);//generates the whole table based of the folder structure
echo drawTopics($dir, "0");//adds the topics which are in the folders
echo "</script>";
?>
<div id="loginPopupContainer"></div>
    <script>
        const baseUrl = 'userManagement/';
    </script>
    
    <script src="userManagement/login.js"></script>
    <script>
    // Wait for the autoCheckToken to complete before checking the role
    loginModule.autoCheckToken().then(() => {
        if ((loginModule.getRole() === "moderator")||(loginModule.getRole() === "admin")) {
            // Redirect the user to another page
            window.location.href = "topics_dozent.php";
        }
    });
</script>
<a href="userManagement/logout.html" style="z-index: 4; position: fixed; top: 10px; right: 10px; text-decoration: none; background-color: #007bff; color: #fff; padding: 10px; border-radius: 5px;">Account</a>

</body>

</html>