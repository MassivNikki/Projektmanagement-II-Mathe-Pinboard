<?php
// Get the current date
$date = date("Y-m-d");
//create the comments directory if necessary
if (!file_exists("comments")) {
    if (!mkdir("comments", 0777, true)) {
        die('Failed to create directory: ' . $directory);
    }
}
//set the filepath
$xmlFile = "./comments/comments-".$date . ".xml";

// Subdirectory path
$subdirectoryPath = './posts/';

// Get the list of PHP files in the subdirectory
$phpFiles = glob($subdirectoryPath . 'post-*.php');



// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" ) {
    if($_POST["type"]=="comment"){
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    
    
    // Create the comment data and ensure it is filled
    if($name == "" || $comment == ""){
            exit();
        } else {
    $commentData = array(
        
        "name" => $name,
        "comment" => $comment,
        "created_at" => date("Y-m-d H:i:s")
    );

    
    // Check if the XML file exists for the current date
    if (file_exists($xmlFile)) {
        // Load the existing XML file
        $xml = simplexml_load_file($xmlFile);
    } else {
        // Create a new XML file
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><comments></comments>');
    }
    
    // Create a new comment element
    $commentElement = $xml->addChild("comment");
    $commentElement->addChild("name", $commentData["name"]);
    $commentElement->addChild("comment", $commentData["comment"]);
    $commentElement->addChild("created_at", $commentData["created_at"]);
    
    // Save the XML file
    $xml->asXML($xmlFile);

    //reempty the variables just in case
    $name = "";
    $comment = "";

    header('Location: anwendung.php');
}
    }
    if($_POST["type"]=="filter"){

        
        
        $filterType = $_POST["orOrAnd"];

        $phpFiles = [];

        $desiredTags = explode(",", $_POST["tags"]);
        
        $desiredTags = array_map('trim', $desiredTags);
    $desiredTags = array_filter($desiredTags);

    // Specify the directory path
    $directory = "./posts/";

    // Get the list of XML files in the directory
    $files = glob($directory . "post-*.xml");

    foreach ($files as $xmlFile) {
        // Load the XML file
        $xml = simplexml_load_file($xmlFile);

        // Get the tags from the XML
        $tags = [];
        foreach ($xml->tag as $tag) {
            $tags[] = (string) $tag;
           
        }


        // Check if the tags match the desired criteria
        $matchingTags = array_intersect($desiredTags, $tags);

        // If the tags match, include the corresponding PHP file. OR works because it is included as long as a single tag matches
        if (!empty($matchingTags) && $filterType == "or") {
            $phpFile = str_replace(".xml", ".php", $xmlFile);
            if (file_exists($phpFile)) {
                $phpFiles[] = $phpFile;
                
            }
        }
                // If the tags match, include the corresponding PHP file. AND works because it is included as long as a the length of the desired tags is as long as the found tags
        elseif($filterType == "and" && count($matchingTags) == count($desiredTags) ){
            $phpFile = str_replace(".xml", ".php", $xmlFile);
            if (file_exists($phpFile)) {
                $phpFiles[] = $phpFile;
            }
        }
    }
}
    
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>PlaceholderTitle</title>
    <link rel="stylesheet" href="../../../postStyle.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <script>
        
//code i got from the guy responsible for the "thema" pages
function changeSiteName(name) {
[].forEach.call(document.querySelectorAll('h1'),
function (e) {
e.innerHTML = name;
});
[].forEach.call(document.querySelectorAll('title'),
function (e) {
e.innerHTML = name;
});
}
</script>
</head>
<body>
    <div class="topicName"><h1>Placeholder</h1></div>
    <h2>Post erstellen</h2>

    <!-- Post submission form -->
    <form method="POST" action="process_post.php" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <br>
        <input type="text" name="name" required><br>

        <label for="text">Text:</label>
        <br>
        <textarea name="text" required></textarea><br>

        <label for="video">Video:</label>
        <br>
        <input type="file" name="videoFile"><br>
        <input type="text" name="video" placeholder="Link des videos"><br>

        <label for="picture">Picture:</label><br>
        <input type="file" name="picture"><br>
        <input type="text" name="pictureLink" placeholder="Link des Bildes"><br>

        <label for="file">File:</label><br>
        <input type="file" name="file"><br>

        <label for="text">Tags:</label><br>
        <textarea name="tags" placeholder="hier tags einfügen. Mit Komma Trennen. z.b. Tag1,Tag2,Tag3"></textarea><br>

        <button type="submit">Posten</button>
    </form>

    <!--Filter form -->
    <h2>Posts</h2>
    <div class="filterArea">
    <form method="POST">
    <label for="tags">Desired Tags:</label><br>
    <input type="hidden" name="type" value="filter">
    <input type="text" name="tags" id="tags" placeholder="Enter tags separated by commas" required><br>
        <label>Filtertype:</label><br>
    <label for="Or" style="width: 45px;">Oder</label>
    <input type="radio" name="orOrAnd" id="Or" value="or" checked="checked" required>
    <label for="And" style="width: 40px;">Und</label>
    <input type="radio" name="orOrAnd" id="And" value="and" required><br>
    <button type="submit">Filtern</button><br>
        <b>Momentane Filter: </b>
    <br>
    <?php if (isset($desiredTags)) {
        //this is here to show your filtered tags
    foreach ($desiredTags as $tag) {
        
        echo "#" . $tag . " ";
    }
}?>
        <a href="anwendung.php">
            <button>Filter zurücksetzen</button>
        </a>
    </form>

</div>




    <?php
//once again code from the "thema" guy
$str = dirname(__FILE__);
$str2 = substr($str, 39);
echo "<script>changeSiteName('".$str2."');</script>";


//Sort the php files via their point value



foreach ($phpFiles as $phpFile) {
    $xmlFileName = str_replace(".php", ".xml", $phpFile);
    // Load the XML file
    if(file_exists($xmlFileName)){
    $xml = simplexml_load_file($xmlFileName);

    // Extract the points value
    $points = (int) $xml->points;

    // Store the snippet filename and points value in an array
    $valuedFiles[] = [
        'filename' => $phpFile,
        'points' => $points,
    ];}
}

// Sort the snippets based on their points value
if(count($phpFiles) != 0){
usort($valuedFiles, function($a, $b) {
    return $b['points'] - $a['points']; // Sort in descending order
});

// Iterate over the sorted snippets and include them
foreach ($valuedFiles as $loadedFile) {
    include $loadedFile['filename'];
    echo "<hr>";
}
}


?>

    <h2>Kommentarsektion</h2>
    <div class="commentInput">
    <!-- Comment form -->
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="name">Name:</label><br>
        <input type="text" name="name" value="" required><br>
        
        <label for="comment">Comment:</label><br>
        <textarea name="comment" value="" required></textarea><br>

        <input type="hidden" name="type" value="comment">
        
        <button type="submit">Kommentieren</button>
    </form>
</div>
    
    <!-- Display comments -->
    <h2>Kommentare:</h2>
    <?php
    $commentFiles = glob('./comments/comments-*.xml');

    foreach($commentFiles as $xmlFileSingle){
        $comments = simplexml_load_file($xmlFileSingle);

        if ($comments !== null && $comments->count() > 0) {
        foreach ($comments->comment as $comment) {
            echo "<div class='comment'><p><strong>".$comment->name."</strong> said:</p>";
            echo "<p class='commentText'>".$comment->comment."</p>";
            echo "<p>Posted on: ".$comment->created_at."</p>";
            echo "</div><hr>";
        }
    }
    }

     if(count($commentFiles)=== 0) {
        echo "Noch keine Kommentare.";
    }
    ?>
</body>
</html>
