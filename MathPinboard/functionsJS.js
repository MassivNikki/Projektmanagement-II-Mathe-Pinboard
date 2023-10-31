let globalName = "";
let globalOldLevel = "";
let editButtonSymbol = "M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 " +
    "                           255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 " +
    "                           31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 " +
    "                           0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152V424c0 48.6 39.4 88 88 88H360c48.6 0 88-39.4" +
    "                           88-88V312c0-13.3-10.7-24-24-24s-24 10.7-24 24V424c0 22.1-17.9 40-40 40H88c-22.1 0-40-17.9-40-40V152c0-22.1 17.9-40 40-40H200c13.3" +
    "                           0 24-10.7 24-24s-10.7-24-24-24H88z";

function newTableRow(rowNum) { //es wird eine neue Reihe unten hinzugefügt
    [].forEach.call(document.querySelectorAll('.mainTable'),
        function (e) {
            e.innerHTML += "<tr>" +
                "              <td id=T" + rowNum + " class='main" + rowNum + "' ondrop=drop(event,this) ondragover=allowDrop(event)>\n" +//ebenen erlauben es themen in sie fallen zu lassen und falls
                // dies passiert, werden sie in die ebene verschoben
                "              <div>\n" +
                "                <button type='submit' title='Add new topic' onclick=" + "openAddWindow('" + rowNum + "') class='addButton' >ADD</button>\n" +//der knopf um neue themen zu adden
                "              </div>\n" +
                "              </td>\n" +
                "          </tr>"
        });
}

function newTopic(name, tags, kind, gtk, description) {//generiert die Objekte für das neue Thema
    let showGtk, showDesc, tagsArray, tagField = "", tempTags = tags, showTags;
    if (tags.length > 0) {
        tags = tags.replaceAll(",", " ");
        tagsArray = tempTags.split(",");
        tagsArray.forEach(element => tagField += "#" + element);
    }
    showGtk = "";
    //falls die dateien leer sind, werden sie auch nicht angezeigt
    if (gtk.length === 0) {
        showGtk = "none";
    }else {
        let tempTopics = gtk.split(",");
        tempTopics.forEach((element,index) => { tempTopics[index] = "<b class=\"topicLink\" onclick=\"searchTopicViaLink('"+element+"')\">"+ element + "</b>"});
        gtk = tempTopics.join(',');
    }
    if (description.length === 0) {
        showDesc = "none";
    }
    if (tags.length === 0) {
        showTags = "none";
    }else{
        let tempTopics = tagField.split("#");
        tempTopics.forEach((element,index) => { tempTopics[index] = "<b class=\"topicLink\" onclick=\"searchTagsViaLink('"+element+"')\">"+ element + "</b>"});
        tagField = tempTopics.join('#');
    }
    let newName = name.replaceAll("_", " ");//das es mit unterstrich übergeben wurde, wird es hier wieder zurück gemacht
    [].forEach.call(document.querySelectorAll('.main' + kind),//es werden alle Elemente in der angegeben ebene ausgewählt und das neue Thema wird angehängt
        function (e) {
            e.innerHTML = e.innerHTML +//alle knöpfe führen beim Drücken entweder php oder js code aus wobei das value im php code verwendet wird
                "<div class='topic " + tags + "'>" +
                "   <div class='top' draggable='true' id='topicDiv" + name + "' ondragstart=drag(event," + kind + ")>" +
                "       <button class='topicName' id='topicNameBtt' title='Go to post' onclick=openPostSite('" + kind + "','" + name + "')>" + newName + "</button>" +//die Beitragsseite öffnet sich beim klicken
                "       <div class='symbolDiv'>" + "" +
                "           <button class='tagButton' title='Show Tags' style='display: " + showTags + "' onclick=" + "changeTagFieldStatus('tag" + encodeURIComponent(name) + "')>#</button>" +//tag button zum anzeigen der vergebenen tags
                "           <form method='POST'>" +
                "               <button class='editBtt' title='Edit Content' type='submit' value='" + kind + ",/T" + kind + "/" + newName + "," + newName + "' name='editTopic'>" +//knopf zum bearbeiten des Themas
                "                   <svg xmlns=\"http://www.w3.org/2000/svg\" height=\"1em\" viewBox=\"0 0 512 512\"><style>svg{fill:#ffffff}</style>" +
                "                       <path d=\"" + editButtonSymbol + "\"/>" +
                "                   </svg>" +
                "               </button>" +
                "               <button type='submit' title='Delete Topic' onclick=" + "openDeleteWindow('" + name + "," + kind + "') class='deleteBtt' >🗑</button>" +//Löschknopf des themas
                "           </form>" +
                "       </div>" +
                "   </div>" +
                "<div ><div style='display: none' class='tagTextField' id='tag" + name + "' >" + tagField + "</div><details open style='display: " + showDesc + "'><summary>Beschreibung</summary>" + description +
                "</details><details style='display: " + showGtk + "'><summary>Sollte man wissen!</summary>" + gtk + "</details>" +
                "</div>" +
                "</div>";
        });
}

function allowDrop(ev) {//gibt an ob man sachen da reinfallen lassen kann
    ev.preventDefault();
}

function drag(ev, level) {//beim nehmen des themas werden die daten des themas global gespeichert
    ev.dataTransfer.setData("text", ev.target.id);
    let div = document.getElementById(ev.target.id);
    globalName = div.getElementsByClassName("topicName")[0].innerHTML;
    globalOldLevel = level;
}

function drop(ev, target) {//wenn man das thema in einer andern ebene fallen lässt wird ein bestätigunsfenster geöffnet
    if (target.id === "T" + globalOldLevel) {
        return;
    }
    openDragAndDropWindow(ev, target.id);
}

function finalDrop(ev) {//das thema wird der ebene angehängt
    ev.preventDefault();
    let data = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(data));
}

function openDragAndDropWindow(ev, newLevel) {//fragt nach ob man das thema wirklich verschieben will

    [].forEach.call(document.querySelectorAll('body'),
        function (e) {
            e.innerHTML = e.innerHTML +
                "<div class='deleteWindow'><p>Move topic <b>'" + globalName + "'</b> to level " + newLevel + "?</p>" +
                "<form method='POST'>" +
                "<input style='display: none' type='text' name='topicName' value='" + globalName + "'>" +
                "<input style='display: none' type='text' name='level' value=" + newLevel + ">" +
                "<button type='submit' onclick=finalDrop(" + ev + ") class='yes' name='moveTopic' value='" + globalOldLevel + "'>Yes</button>" +
                "<button type='submit' name='close' class='no'>No</button>" +
                "</form>" +
                "</div>"


        });
}

function openPostSite(level, name) {//öffnet die beitragsseite
    name = name.replaceAll("_", " ");
    window.open("topics/T" + level + "/" + name + "/anwendung.php", "_self");
}

function changeTagFieldStatus(id) {//das Tag Fenster wird geöffnet oder wieder geschlossen, solange es überhaupt tags gibt
    let e = document.getElementById(id)
    if (e.innerHTML.length > 0) {
        if (e.style.display === "none") {
            e.style.display = "";
        } else (e.style.display = "none")
    }

}

function openEditWindow(name, gtK, tags, desc, level) {//öffnet das Editierfenster mit den übergebenen Werten
    [].forEach.call(document.querySelectorAll('body'),
        function (e) {
            e.innerHTML = e.innerHTML +
                "<div class='addWindow'><div class='addForm'><form style='width: 100%' method='POST'>" +
                "Name<br><input class='textFieldAddW' type='text' name='topicName' id='topicNameField' value='" + name + "' required><br>" +
                "Dependent topics<br><textarea class='textFieldAddW' name='GoodToKnow' placeholder='These are other topics the Students should know to understand this topic!'>" + gtK + "</textarea><br>" +
                "Tags<br><textarea class='textFieldAddW' name='topicTags' placeholder='Please seperate with commas!'>" + tags + "</textarea><br>" +
                "Description<br><textarea class='textFieldAddW' name='topicDescription'>" + desc + "</textarea><br>" +
                "<button class='finishAddBtt' type='submit' name='Add' value='" + level + "'>Confirm</button>" +
                "<button class='cancelBtt' type='submit' name='close' onclick='closeWindow(\"topicNameField\")'>Cancel</button><textarea name='oldName' style='display: none'>" + name + "</textarea></form></div></div>";
        });
}

function openAddWindow(level) {//öffnet das Fenster zum erzeugen eines neuen Themas, wobei die ebene übergeben wird
    [].forEach.call(document.querySelectorAll('body'),
        function (e) {
            e.innerHTML = e.innerHTML +//die einzelnen elemente besitzen werte/namen womit im php code weiter gearbeitet wird
                "<div class='addWindow'><div class='addForm'><form style='width: 100%' method='POST'>" +
                "Name<br><input class='textFieldAddW' type='text' name='topicName' id='topicNameField' required><br>" +
                "Dependent topics<br><textarea class='textFieldAddW' name='GoodToKnow' placeholder='These are other topics the Students should know to understand this topic!'></textarea><br>" +
                "Tags<br><textarea class='textFieldAddW' name='topicTags' placeholder='Please seperate with commas!(i.e. Algebra,Vectors)'></textarea><br>" +
                "Description<br><textarea class='textFieldAddW' name='topicDescription'></textarea><br>" +
                "<button class='finishAddBtt' type='submit' name='Add' value='" + level + "'>Add Topic</button>" +
                "<button class='cancelBtt' type='submit' name='close' onclick='closeWindow(\"topicNameField\")'>Cancel</button></form></div></div>";
        });
}

function closeWindow(topicName) {
    document.getElementById(topicName).required = false;
}

function openDeleteWindow(name) {//öffnet das Fenster, wo man auswählen kann ob man das Objekt wirklich löschen will
    name = name.split(",");//splitten den namen
    name[0] = name[0].replaceAll("_", " ");
    [].forEach.call(document.querySelectorAll('body'),//dafür da, damit das fenster über der gesamten seite angezeigt wird
        function (e) {
            e.innerHTML = e.innerHTML + "<div class='deleteWindow'><p>Do you really want to delete the folder <b>'" + name[0] + "'</b>?</p><form method='POST' class='deleteForm'>" +
                "<button type='submit' class='yes' name='deleteTopic' value='" + name + "'>Yes</button>" +
                "<button type='submit' name='close' class='no'>No</button></form></div>";
        });
}

function searchTopic() {//sucht nach Themen mit diesem namen
    var input, filter, nameButton, searchValue, tags, divs, tagfilter, tagtext, topicName, tagbool = false;
    input = document.getElementById('searchTopic');//nimmt den inhalt der suchleiste
    tags = document.getElementById('searchTag');
    if (tags.value != null) {//falls während de namensuche auch nach einem tag gesucht wird, wird dieser ebenfalls angewendet
        tagbool = true;
        tagfilter = tags.value.toUpperCase();
    }
    filter = input.value.toUpperCase();//der name wird zu einfacheren suche komplett großgeschrieben
    divs = document.getElementsByClassName("topic");//es werden alle themen container genommen
    // Loop through all list items, and hide those who don't match the search query
    for (let i = 0; i < divs.length; i++) {
        nameButton = divs[i].getElementsByTagName("button")[0];//da der name in einem knopf steht wird der erste knopf genommen
        tagtext = divs[i].className.substring(5).toUpperCase();//die tags sind im classnamen drin und beginnen nach der 5. Stelle im namen
        topicName = nameButton.innerText;
        searchValue = topicName.toUpperCase();
        if (searchValue.indexOf(filter) > -1) {//falls das thema mit dem namen existiert und es (falls vorhanden) den tag besitzt wird es angezeigt
            if (tagtext.indexOf(tagfilter) > -1 && tagbool) {
                divs[i].style.display = "";
            }
        } else {//ansonsten nicht
            divs[i].style.display = "none";
        }
    }
}

function searchTopicViaLink(topic){
    let searchBar = document.getElementById('searchTopic');
    searchBar.value = topic;
    searchTopic();
}

function searchTagsViaLink(topic){
    let searchBar = document.getElementById('searchTag');
    searchBar.value = topic;
    searchTag();
}
function searchTag() {//sucht nach themen die den tag besitzen
    //selbe logik wie beim namen suchen, blos umgedreht
    var input, filter, topicName, txtValue, text, topics, topicfilter, divs, topicbool = false;
    input = document.getElementById('searchTag');
    topics = document.getElementById('searchTopic');
    if (topics.value != null) {
        topicbool = true;
        topicfilter = topics.value.toUpperCase();
    }
    filter = input.value.toUpperCase();
    divs = document.getElementsByClassName("topic");

    // Loop through all list items, and hide those who don't match the search query
    for (let i = 0; i < divs.length; i++) {
        topicName = divs[i].getElementsByTagName("button")[0].innerText.toUpperCase();
        text = divs[i].className.substring(5);
        txtValue = text.toUpperCase();
        if (txtValue.indexOf(filter) > -1) {
            if (topicName.indexOf(topicfilter) > -1 && topicbool) {
                divs[i].style.display = "";
            }
        } else {
            divs[i].style.display = "none";
        }
    }
}


//<a href="+"javascript:searchTopicWithLink('p')>test</a>
