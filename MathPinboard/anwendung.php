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
$xmlFile = "./comments/comments-" . $date . ".xml";

// Subdirectory path
$subdirectoryPath = './posts/';

// Get the list of PHP files in the subdirectory
$phpFiles = glob($subdirectoryPath . 'post-*.php');


// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["type"] == "comment") {
        $name = $_POST["name"];
        $comment = $_POST["comment"];


        // Create the comment data and ensure it is filled
        if ($name == "" || $comment == "") {
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
    if ($_POST["type"] == "filter" && $_POST["tags"] != "") {

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
                $tags[] = (string)$tag;

            }


            // Check if the tags match the desired criteria
            $matchingTags = array_intersect($desiredTags, $tags);

            // If the tags match, include the corresponding PHP file. OR works because it is included as long as a single tag matches
            if (!empty($matchingTags) && $filterType == "or") {
                $phpFile = str_replace(".xml", ".php", $xmlFile);
                if (file_exists($phpFile)) {
                    $phpFiles[] = $phpFile;

                }
            } // If the tags match, include the corresponding PHP file. AND works because it is included as long as a the length of the desired tags is as long as the found tags
            elseif ($filterType == "and" && count($matchingTags) == count($desiredTags)) {
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
<h2>Create post</h2>

<!-- Post submission form -->
<form method="POST" action="process_post.php" enctype="multipart/form-data">
    <div class="filesDiv">
            <input type="hidden" name="name" id="postname" value="null" required>
        <div><label for="text">Caption:</label><br>
            <input type="text" name="header" required>
        </div>
    </div>

    <label for="text">Text:</label>
    <br>
    <div class="contentTextDiv"><textarea class="contentText" name="text" required></textarea></div>
    <br>
    <div class="filesDiv">
        <div>
            <label for="video">Video(Youtube):</label><br>
            <input type="text" name="video" placeholder="Embed Link of the videos"></div>
        <div>
            <label for="picture">Picture:</label><br>
            <input type="text" name="pictureLink" placeholder="Link of the picture">
        </div>
    </div>


    <label for="text">Tags:</label><br>
    <textarea name="tags" placeholder="add tags here. Separate with commas. e.g. Tag1,Tag2,Tag3"></textarea><br>

    <button type="submit" class="finishAction">Post</button>
</form>

<!--Filter form -->
<h2>Posts</h2>
<div class="filterArea">
    <form method="POST">
        <label for="tags">Desired Tags:</label><br>
        <input type="hidden" name="type" value="filter">
        <input type="text" name="tags" id="tags" placeholder="Enter tags separated by commas"><br>
        <label>Filtertype:</label><br>
        <label for="Or" style="width: 30px; padding-left: 2px">Or</label>
        <input type="radio" name="orOrAnd" id="Or" value="or" checked="checked" required>
        <label for="And" style="width: 40px;padding-left: 2px">And</label>
        <input type="radio" name="orOrAnd" id="And" value="and" required><br>
        <button type="submit" class="finishAction">Filter</button>
        <br>
        <b>Current filters: </b>
        <br>
        <?php if (isset($desiredTags)) {
            //this is here to show your filtered tags
            foreach ($desiredTags as $tag) {

                echo "#" . $tag . " ";
            }
        } ?>
        <a href="anwendung.php" >
            <button class="resetFilter">reset filter</button>
        </a>
    </form>

</div>


<?php
//once again code from the "thema" guy
$str = dirname(__FILE__);
$str2 = explode("\\", $str);
echo "<script>changeSiteName('" . $str2[count($str2)-1] . "');</script>";


//Sort the php files via their point value


foreach ($phpFiles as $phpFile) {
    $xmlFileName = str_replace(".php", ".xml", $phpFile);
    // Load the XML file
    if (file_exists($xmlFileName)) {
        $xml = simplexml_load_file($xmlFileName);

        // Extract the points value
        $points = (int)$xml->points;

        // Store the snippet filename and points value in an array
        $valuedFiles[] = [
            'filename' => $phpFile,
            'points' => $points,
        ];
    }
}

// Sort the snippets based on their points value
if (count($phpFiles) != 0) {
    usort($valuedFiles, function ($a, $b) {
        return $b['points'] - $a['points']; // Sort in descending order
    });

// Iterate over the sorted snippets and include them
    foreach ($valuedFiles as $loadedFile) {
        include $loadedFile['filename'];
        echo "<hr>";
    }
}


?>

<h2>Comment section</h2>
<div class="commentInput">
    <!-- Comment form -->
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="name" id="commentname" value="null" required><br>

        <label for="comment">Comment:</label><br>
        <textarea name="comment" value="" required></textarea><br>

        <input type="hidden" name="type" value="comment">

        <button type="submit" class="finishAction">Post comment</button>
    </form>
</div>

<!-- Display comments -->
<h2>Comments:</h2>
<?php
$commentFiles = glob('./comments/comments-*.xml');

foreach ($commentFiles as $xmlFileSingle) {
    $comments = simplexml_load_file($xmlFileSingle);

    if ($comments !== null && $comments->count() > 0) {
        foreach ($comments->comment as $comment) {
            echo "<div class='comment'><p><strong>" . $comment->name . "</strong> said:</p>";
            echo "<p class='commentText'>" . $comment->comment . "</p>";
            echo "<p>Posted on: " . $comment->created_at . "</p>";
            echo "</div><hr>";
        }
    }
}

if (count($commentFiles) === 0) {
    echo "Noch keine Kommentare.";
}
?>
<div id="loginPopupContainer"></div>
    <script>
        //standard user management script stuff, along with the "account" button
        const baseUrl = '../../../userManagement/';
    </script>
    
    <script src="../../../userManagement/login.js"></script>
    <script>
        //DOM listener needed here
        document.addEventListener('DOMContentLoaded', function() {
        // Automatically insert correct names for usernames in posts and comments
        loginModule.autoCheckToken().then(() => {
            $username = loginModule.getUsername();
            $role = loginModule.getRole();
            document.getElementById("commentname").value = $username+" ("+$role+")";
            document.getElementById("postname").value =  $username+" ("+$role+")";
            console.log($username);
        });
    });
</script>
<a href="../../../userManagement/logout.html" style="z-index: 4; position: fixed; top: 10px; right: 10px; text-decoration: none; background-color: #007bff; color: #fff; padding: 10px; border-radius: 5px;font-family: Arial, sans-serif;">Account</a>
<!-- Yeah, this button kinda hurts your soul, buuuut doing it this way makes copy-pasting easy -->

    <script>
    // Wait for the autoCheckToken to complete before checking the role
    loginModule.autoCheckToken().then(() => {
        $role = loginModule.getRole();
        //make a link
        var linkElement = document.createElement("a");
        //based on role set the link
        if ($role === "admin" || $role === "moderator") {
        linkElement.href = "../../../topics_dozent.php";
     
    }else{
        linkElement.href = "../../../topics_student.php";
    }
    var buttonElement = document.createElement("button");
    
    // Set the text content of the link
        linkElement.textContent = "Back to topics";
        linkElement.style.color = "#fff";
    // Append the link to the button
        buttonElement.appendChild(linkElement);

        let cssButton = buttonElement.style;
    // Put the button top left
    cssButton.position = "fixed";
        cssButton.top = "10px";
        cssButton.left = "10px";
        cssButton.zIndex = "3";

    //Button doesn't accept style from stylesheet for whatever reason
        cssButton.background = "#dc3f45";
        cssButton.border = "none";
        cssButton.padding = " 10px 15px";
        cssButton.borderRadius = "5px";
        cssButton.fontWeight = "600";
        cssButton.height = "35px";

    

    // Append the button to the body or another element
    document.body.appendChild(buttonElement);   
    });

    </script>
</body>
</html>
