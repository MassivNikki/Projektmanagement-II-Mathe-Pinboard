<?php
function refreshPost($dir)
{
    $topicName = "topicName";
    $topicTags = "topicTags";
    $GtK = "GoodToKnow";
    $description = "topicDescription";
    $oldName = "oldName";
    $level = "level";
    //print_r($_POST);
    if (isset($_POST[$topicName])) {
        $topicName = $_POST[$topicName];
        $topicTags = $_POST[$topicTags];
        $GtK = $_POST[$GtK];
        $description = $_POST[$description];
        $oldName = $_POST[$oldName];
        $level = $_POST[$level];
    }
    foreach ($_POST as $name => $content) { // Most people refer to $key => $value
        switch ($name) {
            case "Add":
                echo $oldName;
                if (!file_exists($dir . '/T' . $content . '/' . $topicName) && !isset($oldName)) {
                    mkdir($dir . '/T' . $content . '/' . $topicName, 0777);
                    copy("anwendung.php", $dir . '/T' . $content . '/' . $topicName . "/anwendung.php");
                    copy("process_post.php", $dir . '/T' . $content . '/' . $topicName . "/process_post.php");
                } else {
                    rename($dir . '/T' . $content . '/' . $oldName, $dir . '/T' . $content . '/' . $topicName);
                }
                if(strlen($topicName) > 0){
                    createFile($dir, $content, $topicName, "tags", $topicTags);
                    createFile($dir, $content, $topicName, "GoodToKnow", $GtK);
                    createFile($dir, $content, $topicName, "Description", $description);
                }

                header('Location: topics_dozent.php');
                break;
            case "moveTopic":
                rename($dir . '/T' . $content . '/' . $topicName, $dir . '/' . $level . '/' . $topicName);
                header('Location: topics_dozent.php');
                break;
            case "deleteTopic":
                $data = explode(",", $content);
                deleteFolder($data[0], $dir . "/T" . $data[1]);
                header('Location: topics_dozent.php');
                break;
            case "editTopic":
                $content = explode(",", $content);
                $name = substr($content[1], 4);
                $gtk = getFileText($dir, $content[1], "GoodToKnow");
                $tags = getFileText($dir, $content[1], "tags");
                $desc = getFileText($dir, $content[1], "description");
                echo "<script>openEditWindow('" . $name . "','" . $gtk . "','" . $tags . "','" . $desc . "','" . $content[0] . "');</script>";
                break;
            case "close":
                header('Location: topics_dozent.php');
                break;
        }

    }
}
function deleteFolder(string $foldername, string $dir)
{
    $handle = opendir($dir);
    while (($entry = readdir($handle)) !== false) {
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess') {
            if ($entry == $foldername) {
                $folder = $dir . "/" . $foldername;
                cleanFolder($folder);
                rmdir($folder);
            }
        }

    }
    closedir($handle);
}

function cleanFolder(string $dir)
{
    $objects = scandir($dir);
    foreach ($objects as $object) {
        if ($object != '.' && $object != '..') {
            if (is_dir($dir . '/' . $object)) {
                cleanFolder($dir . '/' . $object);
                rmdir($dir . '/' . $object);
            } else {
                unlink($dir . '/' . $object);
            }

        }
    }
}

function drawTopics(string $dir, string $kind)
{
    $handle = opendir($dir);
    while (($entry = readdir($handle)) != false && $kind < 10) {
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess') {
            if (is_dir($dir . "/" . $entry)) {
                if (preg_match("/T\d/", $entry)) {
                    drawTopics($dir . "/" . $entry, str_replace("T", "", $entry));
                } else {
                    $name = str_replace(" ", "_",$entry);
                    echo "newTopic('" . $name . "','" . getFileText($dir, $entry, "tags") . "','"
                        . $kind . "','" . getFileText($dir, $entry, "GoodToKnow") . "','" . getFileText($dir, $entry, "description") . "');\n";
                }
            }
        }
    }
    closedir($handle);
}

function drawTableRows(string $dir)
{
    $handle = opendir($dir);
    while (($entry = readdir($handle)) != false) {
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess') {
            if (preg_match("/T\d/", $entry)) {
                echo "newTableRow(" . str_replace("T", "", $entry) . ");";
            }
        }
    }
}

function getFileText($dir, $entry, $fileName)
{
    $File = $dir . "/" . $entry . '/' . $fileName . '.txt';
    $handle = fopen($File, "r");
    $size = filesize($File);
    if ($size > 0) {
        $text = fread($handle, $size);
        fclose($handle);
        return $text;
    }
}


function createFile($dir, $level, $topicName, $fileName, $content)
{
    $path = $dir . '/T' . $level . '/' . $topicName . '/' . $fileName . '.txt';
    if (is_file($path)) {
        unlink($path);
    }

    $newFile = fopen($path, "w");
    fwrite($newFile, $content);
    fclose($newFile);
}


?>
