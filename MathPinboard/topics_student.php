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
$dir = './topics';

$count = iterator_count(new FilesystemIterator($dir));
echo "<table ><tbody class='mainTable'></tbody></table>";
//print_r($_POST);

echo "<script>";
echo drawTableRows($dir);
echo drawTopics($dir, "0");
echo "</script>";

?>

</body>

</html>