<?php
function refreshPost($dir) //jedes mal, wenn die seite aktualisiert wird bzw eine Post request geschickt wurde, wird geprüft um welche Art von Request es sich handelt
{                           //basierend auf der Request wird bestimmter code ausgeführt
    $topicName = "topicName";
    $topicTags = "topicTags";
    $GtK = "GoodToKnow";
    $description = "topicDescription";
    $oldName = "oldName";
    $level = "level";
    //print_r($_POST);
    if (isset($_POST[$topicName])) {// wenn es eine post request gibt, wird geschaut welche daten vorhanden sind, wobei der name pflicht ist.
        $topicName = $_POST[$topicName];
        $topicTags = $_POST[$topicTags];
        $GtK = $_POST[$GtK];
        $description = $_POST[$description];
        $oldName = $_POST[$oldName];
        $level = $_POST[$level];
    }
    //$content ist immer von dem value des buttons im js code abhängig
    foreach ($_POST as $name => $content) { // geht dir request in name -> value paaren durch
        switch ($name) {
            case "Add"://in diesem Fall wird ein Thema hinzugefügt oder umbenannt
                echo $oldName;
                if (!file_exists($dir . '/T' . $content . '/' . $topicName) && !isset($oldName)) {//falls es das thema noch nicht gibt wird ein ordner erstellt und die Dateien der
                                                                                                            // Beitragsseite reinkopiert
                    mkdir($dir . '/T' . $content . '/' . $topicName, 0777);
                    copy("anwendung.php", $dir . '/T' . $content . '/' . $topicName . "/anwendung.php");
                    copy("process_post.php", $dir . '/T' . $content . '/' . $topicName . "/process_post.php");
                } else {
                    rename($dir . '/T' . $content . '/' . $oldName, $dir . '/T' . $content . '/' . $topicName);//falls die action eine Umbenennung ist, wird der ordner umbenannt
                }
                if(strlen($topicName) > 0){//die zusätzlichen dateien werden angelegt
                    createFile($dir, $content, $topicName, "tags", $topicTags);
                    createFile($dir, $content, $topicName, "GoodToKnow", $GtK);
                    createFile($dir, $content, $topicName, "Description", $description);
                }

                header('Location: topics_dozent.php');//Seite wird neu geladen, damit die ansicht aktualisiert wird
                break;
            case "moveTopic"://hier wird ein Thema in eine andere Ebenen verschoben
                rename($dir . '/T' . $content . '/' . $topicName, $dir . '/' . $level . '/' . $topicName);
                header('Location: topics_dozent.php');
                break;
            case "deleteTopic"://das Thema wird gelöscht
                $data = explode(",", $content);// content ist in dem Fall "Name, Ebene"
                deleteFolder($data[0], $dir . "/T" . $data[1]);
                header('Location: topics_dozent.php');
                break;
            case "editTopic":
                $content = explode(",", $content);// content ist in dem Fall "Ebene, Verzeichnis, Name"
                $name = $content[2];
                $gtk = getFileText($dir, $content[1], "GoodToKnow");//nimmt den inhalt der datei
                $tags = getFileText($dir, $content[1], "tags");
                $desc = getFileText($dir, $content[1], "description");
                echo "<script>openEditWindow('" . $name . "','" . $gtk . "','" . $tags . "','" . $desc . "','" . $content[0] . "');</script>";
                break;
            case "close":
                header('Location: topics_dozent.php');//die seite wird neu geladen wodurch sich das Fenster schließt und jegliche daten in der Request gelöscht werden
                break;
        }

    }
}
function deleteFolder(string $foldername, string $dir)//löscht den übergebenen ordner
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

function cleanFolder(string $dir)//löscht den inhalt des angegebenen ordners
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

function drawTopics(string $dir, string $kind)// fügt die einzelnen Themen in die Ebenen ein
{
    $handle = opendir($dir);
    while (($entry = readdir($handle)) != false && $kind < 10) {//falls die Ebene des Themas größer ist als die anzahl der Ebenen, kann das Thema nicht angelegt werden
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess') {//geht jedes Objekt im ordner durch außer bestimmte dateien
            if (is_dir($dir . "/" . $entry)) {//
                if (preg_match("/T\d/", $entry)) {//falls es sich um einen "Ebenen" Ordner handelt wird auf diesen die methode angewendet
                    drawTopics($dir . "/" . $entry, str_replace("T", "", $entry));
                } else {
                    $name = str_replace(" ", "_",$entry);//leerzeichen werden mit dem unterstrich ersetzt damit später keine Probleme entstehen
                    echo "newTopic('" . $name . "','" . getFileText($dir, $entry, "tags") . "','"
                        . $kind . "','" . getFileText($dir, $entry, "GoodToKnow") . "','" . getFileText($dir, $entry, "description") . "');\n";
                    //die js Methode wird in die "script" Tags geschrieben, wodurch sie ausgeführt wird
                }
            }
        }
    }
    closedir($handle);
}

function drawTableRows(string $dir)//fügt die ebenen in die Seite ein
{
    $handle = opendir($dir);//öffnet das übergebene verzeichnis
    while (($entry = readdir($handle)) != false) {
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess') {//falls das verzeichnis zu öffnen geht, ist $entry der jeweilige eintrag im verzeichnis
                                                                        //bestimmte dateien werden übersprungen, da sie nicht wichtig sind
            if (preg_match("/T\d/", $entry)) {//falls es sich um einen "Zeilen" ordner handelt wird mit der js Methode eine neue Zeile auf der Website generiert
                echo "newTableRow(" . str_replace("T", "", $entry) . ");";
            }
        }
    }
}

function getFileText($dir, $entry, $fileName)//öffnet die übergebenen Datei und gibt den Inhalt zurück solange etwas drin steht
{
    $File = $dir . "/" . $entry . '/' . $fileName . '.txt';
    $handle = fopen($File, "r");//öffnet die datei zum lesen
    $size = filesize($File);
    if ($size > 0) {
        $text = fread($handle, $size);//list die datei bis zur vorher bestimmten länge des inhaltes
        fclose($handle);
        return $text;
    }
    return "";
}


function createFile($dir, $level, $topicName, $fileName, $content)//eine neue Text datei wird angelegt und Inhalt reingeschrieben
{
    $path = $dir . '/T' . $level . '/' . $topicName . '/' . $fileName . '.txt';
    if (is_file($path)) {//falls die datei schon existiert wird sie gelöscht(ist fürs editieren wichtig)
        unlink($path);
    }
    $newFile = fopen($path, "w");//die datei wird am gewünschten Pfad angelegt
    fwrite($newFile, $content);//die datei wird beschrieben mit dem inhalt
    fclose($newFile);
}


?>
