<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="topics.css">
    <script type="text/javascript" src="functionsJS.js"></script>
    <title>Themenauswahl</title>
</head>

<body>

<input class="searchBar" type="text" id="searchTopic" onkeyup="searchTopic()" placeholder="Search for Topics">
<input class="searchBar" type="text" id="searchTag" onkeyup="searchTag()" placeholder="Search for Tags"><br><br>
<?php
require '/xampp/htdocs/mathpinboard/functions.php';
$dir = '/xampp/htdocs/mathpinboard/topics';

$count = iterator_count(new FilesystemIterator($dir));
refreshPost($dir);
echo "<table ><tbody class='mainTable'></tbody></table>";
//print_r($_POST);

    echo "<script>";
    echo drawTableRows($dir);
    echo drawTopics($dir, "0");
    echo "</script>";

//abhangigkeitenliste bei jedem topic mit reinpacken, (allgemein in den files suchen und nicht in den klassen)
?>

</body>

</html>