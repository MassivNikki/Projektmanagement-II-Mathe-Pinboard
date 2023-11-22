let globalName = "";
let globalOldLevel = "";

//this method adds a new Row to the Topic table based of the folders in xampp every time it is called
function newTableRow(rowNum) {
    [].forEach.call(document.querySelectorAll('.mainTable'),
        function (e) {
            e.innerHTML += "<tr>" +
                //the rows have the ability to get topics dropped into them when a topic is dragged over the row it is allowed to get dropped into the row(allowDrop)
                // when its released topic will be added to that row and deleted from the original row
                "              <td id=T" + rowNum + " class='main" + rowNum + "' ondrop=drop(event,this) ondragover=allowDrop(event)>\n" +
                "              <div>\n" +
                // on first generation the row gets an "add" button for adding new Topics to that row
                "                <button type='submit' title='Add new topic' onclick=" + "openAddWindow('" + rowNum + "') class='addButton' >ADD</button>\n" +
                "              </div>\n" +
                "              </td>\n" +
                "          </tr>"
        });
}
//this method adds a new Topic to the row, based of the folders in xampp it is called in
function newTopic(name, tags, kind, gtk, description) {
    let showGtk, showDesc, tagsArray, tagField = "", tempTags = tags, showTags;
    //if there are no tags, the field is not shown, else every tags gets a # in front
    if (tags.length > 0) {
        tags = tags.replaceAll(",", " ");
        tagsArray = tempTags.split(",");
        tagsArray.forEach(element => tagField += "#" + element);
    }
    showGtk = "";
    //if the files are empty, there will be no field or button to see
    if (gtk.length === 0) {
        showGtk = "none";
    }else {
        let tempTopics = gtk.split(",");
        //every listed topic will get a function, so if clicked, the topic will be searched
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
        //every listed tag will get a function, so if clicked, the tag will be searched
        tempTopics.forEach((element,index) => { tempTopics[index] = "<b class=\"topicLink\" onclick=\"searchTagsViaLink('"+element+"')\">"+ element + "</b>"});
        tagField = tempTopics.join('#');
    }
    //for saving purposes the name gets an underscore for space and here it will be reverted for better display
    let newName = name.replaceAll("_", " ");
    //all elements that match the search criteria get selected and the new topic will be added to these rows(normally just one)
    [].forEach.call(document.querySelectorAll('.main' + kind),
        function (e) {
            e.innerHTML = e.innerHTML +//alle knöpfe führen beim Drücken entweder php oder js code aus wobei das value im php code verwendet wird
                "<div class='topic " + tags + "'>" +
                //every topic is able to get dragged (draggable) so it can get moved to another row
                "   <div class='top' draggable='true' id='topicDiv" + name + "' ondragstart=drag(event," + kind + ")>" +
                //the button with the topic name gets a function so with a click, the post site will be opened
                "       <button class='topicName' id='topicNameBtt' title='Go to post' onclick=openPostSite('" + kind + "','" + name + "')>" + newName + "</button>" +
                "       <div class='symbolDiv'>" + "" +
                //the tag field is normally hidden, so with this button it will be shown
                "           <button class='tagButton' title='Show Tags' style='display: " + showTags + "' onclick=" + "changeTagFieldStatus('tag" + encodeURIComponent(name) + "')>#</button>" +
                //because the edit function changes the data in files it has to be in a php POST
                "           <form method='POST'>" +
                //with this button the data of the topic can be changed
                "               <button class='editBtt' title='Edit Content' type='submit' value='" + kind + ",/T" + kind + "/" + newName + "," + newName + "' name='editTopic'>✎" +
                "               </button>" +
                //this button deletes the topic after asking for confirmation
                "               <button type='submit' title='Delete Topic' onclick=" + "openDeleteWindow('" + name + "," + kind + "') class='deleteBtt' >🗑</button>" +
                "           </form>" +
                "       </div>" +
                "   </div>" +
                //the text fields with the tags and topics which are good to know
                "<div ><div style='display: none' class='tagTextField' id='tag" + name + "' >" + tagField + "</div><details open style='display: " + showDesc + "'><summary>Beschreibung</summary>" + description +
                "</details><details style='display: " + showGtk + "'><summary>Sollte man wissen!</summary>" + gtk + "</details>" +
                "</div>" +
                "</div>";
        });
}

//elements with this function can get items dropped onto them
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev, level) {
    //when a topic is dragged the data of that topic will be globally saved so the final row knows what topic it got
    ev.dataTransfer.setData("text", ev.target.id);
    let div = document.getElementById(ev.target.id);
    globalName = div.getElementsByClassName("topicName")[0].innerHTML;
    globalOldLevel = level;
}

function drop(ev, target) {

    //if the topic gets dropped into the same row, nothing happens, and it is the same as before
    if (target.id === "T" + globalOldLevel) {
        return;
    }
    //if the topic is dropped a confirmation window will open
    openDragAndDropWindow(ev, target.id);
}

function finalDrop(ev) {//the topic gets added to the row and deleted from the original row
    ev.preventDefault();
    let data = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(data));
}

function openDragAndDropWindow(ev, newLevel) {//asks for confirmation for the drop

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

function openPostSite(level, name) {//opens the post site
    name = name.replaceAll("_", " ");
    window.open("topics/T" + level + "/" + name + "/anwendung.php", "_self");
}

function changeTagFieldStatus(id) {
    //changes the status of the tag field, so it is shown when clicked onto the button
    let e = document.getElementById(id)
    if (e.innerHTML.length > 0) {
        if (e.style.display === "none") {
            e.style.display = "";
        } else (e.style.display = "none")
    }

}

function openEditWindow(name, gtK, tags, desc, level) {
    //opens the edit window where the user can change the name, description, tags, and the topics that are good to know
    [].forEach.call(document.querySelectorAll('body'),
        function (e) {
            e.innerHTML = e.innerHTML +
                "<div class='addWindow'><div class='addForm'><form style='width: 100%' method='POST'>" +
                "Name<br><input class='textFieldAddW' type='text' name='topicName' id='topicNameField' value='" + name + "' required><br>" +
                "Dependent topics<br><textarea class='textFieldAddW' name='GoodToKnow' placeholder='These are other topics the Students should know to understand this topic!(seperate with commas)'>" + gtK + "</textarea><br>" +
                "Tags<br><textarea class='textFieldAddW' name='topicTags' placeholder='Please seperate with commas!'>" + tags + "</textarea><br>" +
                "Description<br><textarea class='textFieldAddW' name='topicDescription'>" + desc + "</textarea><br>" +
                //confirms the edit action and changes the data through php
                "<button class='finishAddBtt' type='submit' name='Add' value='" + level + "'>Confirm</button>" +
                //cancels the edit action and closes the window through php
                "<button class='cancelBtt' type='submit' name='close' onclick='closeWindow(\"topicNameField\")'>Cancel</button><textarea name='oldName' style='display: none'>" + name + "</textarea></form></div></div>";
        });
}

function openAddWindow(level) {//opens the window, where the user can add a new topic to the row
    [].forEach.call(document.querySelectorAll('body'),
        function (e) {
            e.innerHTML = e.innerHTML +//the user fills the fields, which then will be used by the php code
                "<div class='addWindow'><div class='addForm'><form style='width: 100%' method='POST'>" +
                "Name<br><input class='textFieldAddW' type='text' name='topicName' id='topicNameField' required><br>" +
                "Dependent topics<br><textarea class='textFieldAddW' name='GoodToKnow' placeholder='These are other topics the Students should know to understand this topic!'></textarea><br>" +
                "Tags<br><textarea class='textFieldAddW' name='topicTags' placeholder='Please seperate with commas!(i.e. Algebra,Vectors)'></textarea><br>" +
                "Description<br><textarea class='textFieldAddW' name='topicDescription'></textarea><br>" +
                //confirms the action
                "<button class='finishAddBtt' type='submit' name='Add' value='" + level + "'>Add Topic</button>" +
                //cancels the action
                "<button class='cancelBtt' type='submit' name='close' onclick='closeWindow(\"topicNameField\")'>Cancel</button></form></div></div>";
        });
}

function closeWindow(topicName) {//changes "required" field
    //because the name field is required, the window will not close until the required tag is removed
    document.getElementById(topicName).required = false;
}

function openDeleteWindow(name) {//opens the delete window for confirmation
    name = name.split(",");
    name[0] = name[0].replaceAll("_", " ");
    [].forEach.call(document.querySelectorAll('body'),//shows window on top of the whole page
        function (e) {
            e.innerHTML = e.innerHTML + "<div class='deleteWindow'><p>Do you really want to delete the folder <b>'" + name[0] + "'</b>?</p><form method='POST' class='deleteForm'>" +
                "<button type='submit' class='yes' name='deleteTopic' value='" + name + "'>Yes</button>" +
                "<button type='submit' name='close' class='no'>No</button></form></div>";
        });
}

function searchTopic() {//search method for searching topic names in all topics
    var input, filter, nameButton, searchValue, tags, divs, tagfilter, tagtext, topicName, tagbool = false;
    input = document.getElementById('searchTopic');//takes data from the searchbar
    tags = document.getElementById('searchTag');//is needed so the tags will also be considered while searching for a topic
    if (tags.value != null) {//if there is a tag in the search bar it will be added to the whole search action
        tagbool = true;
        tagfilter = tags.value.toUpperCase();
    }
    filter = input.value.toUpperCase();//for easier searching
    divs = document.getElementsByClassName("topic");//all topics get added to an array
    // Loop through all list items, and hide those who don't match the search query
    for (let i = 0; i < divs.length; i++) {
        nameButton = divs[i].getElementsByTagName("button")[0];//name of the topic buttons gets taken, because it has the name in it
        tagtext = divs[i].className.substring(5).toUpperCase();//tha tags are in the class name of the element beginning at the 5. spot
        topicName = nameButton.innerText;
        searchValue = topicName.toUpperCase();
        if (searchValue.indexOf(filter) > -1) {//if the searched string is contained in a topic the topic is shown
            if (tagtext.indexOf(tagfilter) > -1 && tagbool) {
                divs[i].style.display = "";
            }
        } else {//else its hidden
            divs[i].style.display = "none";
        }
    }
}

function searchTopicViaLink(topic){//the searchbar gets the name of the clicked element and searches after it
    let searchBar = document.getElementById('searchTopic');
    searchBar.value = topic;
    searchTopic();
}

function searchTagsViaLink(topic){//the searchbar gets the name of the clicked element and searches after it
    let searchBar = document.getElementById('searchTag');
    searchBar.value = topic;
    searchTag();
}
function searchTag() {//searches for tags in the topics
    //same logic as the topic search, except tags and topics are switched
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
