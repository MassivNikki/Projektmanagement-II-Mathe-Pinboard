<?php
// Handle post submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form input
    $name = $_POST["name"];
    $text = $_POST["text"];
    $video = $_POST["video"];
    $pictureFile = $_FILES["picture"]["name"];
    $fileFile = $_FILES["file"]["name"];
    $picture = $_POST["pictureLink"];
    $videoFile = $_FILES["videoFile"]["name"];
    $tags = $_POST["tags"];

    $folderPath = './posts/';

    //if the posts directory doesnt exist, make it. If you cant, stop and die
    if (!file_exists("posts")) {
        if (!mkdir("posts", 0777, true)) {
            die('Failed to create directory: ' . $directory);
        }
    }


//find the post number for this one specific post. The number will not change and will full up the lowst free space
$postNumber = 1;
while(file_exists($folderPath.'post-'.$postNumber.'.php')){
    $postNumber = $postNumber+1;
}
//set the paths and file names
$generalPath ="./posts/post-".$postNumber;
$filePath = $generalPath.".php";
$xmlPath = $generalPath.".xml";


    //check if the uploads directory exits and place if necessary

    if (!file_exists("uploads")) {
        if (!mkdir("uploads", 0777, true)) {
            die('Failed to create directory: ' . $directory);
        }
    }

    // Upload picture file
    $uploadDir = "uploads/"; // Directory to store the uploaded files
    move_uploaded_file($_FILES["picture"]["tmp_name"], $uploadDir . $pictureFile);

    // Upload file file
    move_uploaded_file($_FILES["file"]["tmp_name"], $uploadDir . $fileFile);

    // Upload video file
    move_uploaded_file($_FILES["videoFile"]["tmp_name"], $uploadDir . $videoFile);


    //deleteMessage
    $deleteMessage = "'Bist du dir sicher dass du diesen Post löschen willst?'";


    // Generate php code (I know its long and stupid, but it works)
    $phpCode = "<div class='post'>\n";
    $phpCode .= '<?php
    $filename = "posts/post-'.$postNumber.'.xml";
    // Process the form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["type"]) && ($_POST["type"]=="delete" || $_POST["type"]=="upvote'.$postNumber.'" || $_POST["type"]=="downvote'.$postNumber.'")) {
        if($_POST["type"]=="delete"){
        $fileToDelete = "'.$filePath.'";
        $fileToBeDeleted = $_POST["fileToBeDeleted"];
        $XMLToDelete = str_replace(".php", ".xml", $fileToDelete);
    
        //this is a needed if, to ensure not all files are deleted when you click one delete button
        if($fileToBeDeleted === $fileToDelete){
        if (!empty($fileToDelete)) {
            
    
            // Delete the file
            if (unlink($fileToDelete)) {
                echo "Post erfolgreich gelöscht.";
            } else {
                echo "konnte Post nicht löschen.";
            }
            if (unlink($XMLToDelete)) {
                echo "Post erfolgreich gelöscht.";
            } else {
                echo "konnte Post nicht löschen.";
            }
        } else {
            echo "Dateipfad ist nicht vorhanden.";
        }
    }
    //link to the same ssite, to clear the cache and ensure the form wont be pre-filled out
    header("Location: anwendung.php");
}
//these are around everywhere and assure, that theres nothing wonky going on with a "form" (site-function) triggering something else
    if($_POST["type"]=="upvote'.$postNumber.'"){
        // Specify the name of the XML file


// Load the XML file
$xml = simplexml_load_file($filename);

// Get the current points value
$points = (int) $xml->points;

// Increment or decrement the points value
$points += 1; // Increment by 1

// Update the points value in the XML
$xml->points = $points;

// Save the updated XML to the file
$xml->asXML($filename);

header("Location: anwendung.php");

    }
    
    if($_POST["type"]=="downvote'.$postNumber.'"){
        // Specify the name of the XML file


// Load the XML file
$xml = simplexml_load_file($filename);

// Get the current points value
$points = (int) $xml->points;

// Decrement or decrement the points value
$points -= 1; // Decrement by 1


// Update the points value in the XML
$xml->points = $points;

// Save the updated XML to the file
$xml->asXML($filename);

header("Location: anwendung.php");

    }

    }
    
    if(file_exists($filename)){
        $xml = simplexml_load_file($filename);

// Get the current points value
$pointsLoaded = (int) $xml->points;
// Output the updated points value
echo "<b> Points: $pointsLoaded </b>";
    }
    ?>
    <h3> Post-'.$postNumber.'</h3>
    <!-- php form with the file path input and delete button -->
    <div  class="deleteBtn">
    <form method="POST">
    <input type="hidden" name="fileToBeDeleted" value="'.$filePath.'">
    <input type="hidden" name="type" value="delete">
        <button type="submit" onclick="return confirm('.$deleteMessage.')">Post Löschen</button>
    </form>
    </div>
    <div class="upvoteBtn">
    <form method="POST" >
    <input type="hidden" name="type" value="upvote'.$postNumber.'">
        <button type="submit">Points +1</button>
    </form>
    </div>
    <div class="downvoteBtn">
    <form method="POST" >
    <input type="hidden" name="type" value="downvote'.$postNumber.'">
        <button type="submit">Points -1</button>
    </form>
    </div>
    ';
    //further string addition stuff
    $phpCode .= "<p><strong>".$name."</strong></p>\n";
    $phpCode .= "<p>".$text."</p>\n";
    if (!empty($video)) {
        $phpCode .= "<iframe src='".$video."'></iframe>\n";
        $tags .= ",Video";
    }
    if (!empty($videoFile)) {
        $phpCode .= "<video src= '".$uploadDir.$videoFile."' controls></video>";
        $tags .= ",Video";
    }
    if (!empty($pictureFile)) {
        $phpCode .= "<img src='".$uploadDir.$pictureFile."' alt='Picture'>\n";
        $tags .= ",Bild";
    }
    if (!empty($picture)) {
        $phpCode .= "<img src='".$picture."'></img>\n";
        $tags .= ",Bild";
    }
    if (!empty($fileFile)) {
        $phpCode .= "<a href='".$uploadDir.$fileFile."'>Download File</a>\n<br>";
        $tags .= ",Datei";
    }

    
    //If theres already a xml file with the name, delete it. THis is in case deletion goes wrong
    if(file_exists($xmlPath)){
        unlink($xmlPath);
    }

    //Tags Block
    $tagsArray = explode(",", $tags); // Convert the string to an array by splitting at commas
    
    // Remove duplicate tags
    $tagsArray = array_unique($tagsArray);
    
    // Trim whitespace from each tag and remove any empty elements
    $tagsArray = array_map('trim', $tagsArray);
    $tagsArray = array_filter($tagsArray);
    
    //set points
    $points = 0;
    
    // Create a SimpleXMLElement object
    $xml = new SimpleXMLElement('<data></data>');
    
    // Add the points as a child element
    $xml->addChild('points', $points);
    
    // Loop through the tags and add them as child elements
    foreach ($tagsArray as $tag) {
        $xml->addChild('tag', $tag);
    }
    
    // Convert the XML object to a formatted XML string
    $xmlString = $xml->asXML();
    
    // Save the XML string to a file
    file_put_contents($xmlPath, $xmlString);
    

    

    $phpCode .= "<div class='tags'>Tags: <br>";

    foreach($tagsArray as $tag){
        $phpCode .= " #".$tag;
    }

    $phpCode .= "</div></div>";

    //Ende Tags
    // Überprüfen, ob die Datei bereits existiert
if (!file_exists($filePath)) {
    // Die Datei öffnen und den Inhalt schreiben
    $file = fopen($filePath, "w");

    if ($file) {
        // Den Inhalt in die Datei schreiben
        fwrite($file, $phpCode);
        
        // Die Datei schließen
        fclose($file);

        echo "<h2> Der Beitrag wurde erfolgreich gepostet. </h2>";
    } else {
        echo "<h2> Fehler, beim öffnen der Post-Datei. </h2>";
    }
} else {
    echo "<h2> Fehler, bei den internen Systemen. </h2>";
    //Der Fehler poppt, wenn die datei bereits existiert, was sie wegen der automatischen Nummerierung nicht sollte
}

    //Create a link to the main site and print the code and the link
    $linkBack = "<a href='anwendung.php' target='_self'>Zurück</a><br><br>";

    echo "<br><br><h2> Danke für ihren Post!<br><br> </h2>";
    include($filePath);
    echo $linkBack;








}
?>
<!DOCTYPE html>
<html>
<head>
    <!--Just a bit of head stuff, to allow for css and similar -->
    <title>Danke für ihren Post!</title>
    <link rel="stylesheet" href="../../../postConfirmation.css">
</head>
</html>
