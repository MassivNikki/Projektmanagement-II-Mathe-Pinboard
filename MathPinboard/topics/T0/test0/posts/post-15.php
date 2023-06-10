<div class='post'>
<?php
    $filename = "posts/post-15.xml";
    // Process the form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["type"]) && ($_POST["type"]=="delete" || $_POST["type"]=="upvote15" || $_POST["type"]=="downvote15")) {
        if($_POST["type"]=="delete"){
        $fileToDelete = "./posts/post-15.php";
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
    if($_POST["type"]=="upvote15"){
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
    
    if($_POST["type"]=="downvote15"){
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
    <h3> Post-15</h3>
    <!-- php form with the file path input and delete button -->
    <div  class="deleteBtn">
    <form method="POST">
    <input type="hidden" name="fileToBeDeleted" value="./posts/post-15.php">
    <input type="hidden" name="type" value="delete">
        <button type="submit" onclick="return confirm('Bist du dir sicher dass du diesen Post löschen willst?')">Post Löschen</button>
    </form>
    </div>
    <div class="upvoteBtn">
    <form method="POST" >
    <input type="hidden" name="type" value="upvote15">
        <button type="submit">Points +1</button>
    </form>
    </div>
    <div class="downvoteBtn">
    <form method="POST" >
    <input type="hidden" name="type" value="downvote15">
        <button type="submit">Points -1</button>
    </form>
    </div>
    <p><strong>fff</strong></p>
<p>fff</p>
<div class='tags'>Tags: <br></div></div>