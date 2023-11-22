<?php
function refreshPost($dir)
{
    //Every time the page is updated or a post request is sent, it is checked what type of request it is and certain code is executed based on the request
    $topicName = "topicName";
    $topicTags = "topicTags";
    $GtK = "GoodToKnow";
    $description = "topicDescription";
    $oldName = "oldName";
    $level = "level";
    //print_r($_POST);
    if (isset($_POST[$topicName])) {// If there is a post request, it will be checked which data is available, whereby the name is mandatory.
        $topicName = $_POST[$topicName];
        $topicTags = $_POST[$topicTags];
        $GtK = $_POST[$GtK];
        $description = $_POST[$description];
        $oldName = $_POST[$oldName];
        $level = $_POST[$level];
    }
    //$content is the value from the js button
    foreach ($_POST as $name => $content) { //goes through the request in name -> value pairing
        switch ($name) {
            case "Add"://in this case a new topic gets added
                echo $oldName;
                //If the topic does not yet exist, a folder will be created and the files from the post page will be copied into it
                if (!file_exists($dir . '/T' . $content . '/' . $topicName) && !isset($oldName)) {
                    mkdir($dir . '/T' . $content . '/' . $topicName, 0777);
                    copy("anwendung.php", $dir . '/T' . $content . '/' . $topicName . "/anwendung.php");
                    copy("process_post.php", $dir . '/T' . $content . '/' . $topicName . "/process_post.php");
                } else {
                    //if the folder exists than it is a rename action and the folder gets renamed
                    rename($dir . '/T' . $content . '/' . $oldName, $dir . '/T' . $content . '/' . $topicName);
                }
                //the additional files get added if there is a topic to be added
                if(strlen($topicName) > 0){
                    createFile($dir, $content, $topicName, "tags", $topicTags);
                    createFile($dir, $content, $topicName, "GoodToKnow", $GtK);
                    createFile($dir, $content, $topicName, "Description", $description);
                }
                //site gets reloaded so the content gets updated and the Post request doesn't get triggered twice
                header('Location: topics_dozent.php');
                break;
            case "moveTopic"://this is the drag and drop action -> the topic gets moved to another folder
                rename($dir . '/T' . $content . '/' . $topicName, $dir . '/' . $level . '/' . $topicName);
                header('Location: topics_dozent.php');
                break;
            case "deleteTopic"://the topic gets deleted
                $data = explode(",", $content);// content is "name, level" in this case
                deleteFolder($data[0], $dir . "/T" . $data[1]);
                header('Location: topics_dozent.php');
                break;
            case "editTopic":
                $content = explode(",", $content);// content is "level, directory, name"
                $name = $content[2];
                $gtk = getFileText($dir, $content[1], "GoodToKnow");
                $tags = getFileText($dir, $content[1], "tags");
                $desc = getFileText($dir, $content[1], "description");
                echo "<script>openEditWindow('" . $name . "','" . $gtk . "','" . $tags . "','" . $desc . "','" . $content[0] . "');</script>";
                break;
            case "close":
                header('Location: topics_dozent.php');//while the page gets reloaded the in-page window will be closed
                break;
        }

    }
}
function deleteFolder(string $foldername, string $dir)//deletes the folder
{
    $handle = opendir($dir);
    while (($entry = readdir($handle)) !== false) {
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess') {
            if ($entry == $foldername) {
                $folder = $dir . "/" . $foldername;
                cleanFolder($folder);//removes all data out of the folder
                rmdir($folder);//deletes the folder
            }
        }

    }
    closedir($handle);
}

function cleanFolder(string $dir)//cleans the folder
{
    $objects = scandir($dir);
    foreach ($objects as $object) {
        if ($object != '.' && $object != '..') {
            if (is_dir($dir . '/' . $object)) {
                //folders cant be deleted if it has content, so it goes into the folder and deletes it whole content and deletes it afterward
                cleanFolder($dir . '/' . $object);
                rmdir($dir . '/' . $object);
            } else {
                //deletes the file
                unlink($dir . '/' . $object);
            }

        }
    }
}

function drawTopics(string $dir, string $kind)//adds the topics into the rows
{
    $handle = opendir($dir);
    while (($entry = readdir($handle)) != false && $kind < 10) {//if the level of the topic is out of the range of existing rows, it cant be added
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess') {//goes through every file in the folder
            if (is_dir($dir . "/" . $entry)) {//
                if (preg_match("/T\d/", $entry)) {//if it is another "level" folder, it goes into it and repeats the process in it
                    drawTopics($dir . "/" . $entry, str_replace("T", "", $entry));
                } else {
                    $name = str_replace(" ", "_",$entry);//space is replaced with underscore, so later problems are prevented
                    //the javaScript method is called with all the data of the folder
                    //with the js function it is shown on the page of the user
                    echo "newTopic('" . $name . "','" . getFileText($dir, $entry, "tags") . "','"
                        . $kind . "','" . getFileText($dir, $entry, "GoodToKnow") . "','" . getFileText($dir, $entry, "description") . "');\n";
                }
            }
        }
    }
    closedir($handle);
}

function drawTableRows(string $dir)//adds the rows to the whole table
{
    $handle = opendir($dir);//opens the directory with the topics
    while (($entry = readdir($handle)) != false) {
        // $entry is the current object if the directory can be opened
        // specific files are skipped
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess') {
            //if it is a folder with the specific name a new table row will be added to the table
            if (preg_match("/T\d/", $entry)) {
                echo "newTableRow(" . str_replace("T", "", $entry) . ");";
            }
        }
    }
}

function getFileText($dir, $entry, $fileName)//opens the file and returns it content if it has any
{
    $File = $dir . "/" . $entry . '/' . $fileName . '.txt';
    $handle = fopen($File, "r");//opens the file with the data
    $size = filesize($File);
    if ($size > 0) {
        $text = fread($handle, $size);//reads the file and returns the content
        fclose($handle);
        return $text;
    }
    return "";
}


function createFile($dir, $level, $topicName, $fileName, $content)//a new text file is added and is filled with the given data
{
    $path = $dir . '/T' . $level . '/' . $topicName . '/' . $fileName . '.txt';
    if (is_file($path)) {//if the file already exists it gets deleted(important for editing)
        unlink($path);
    }
    $newFile = fopen($path, "w");//creates file at specific part
    fwrite($newFile, $content);
    fclose($newFile);
}


?>
