let globalName = "";
let globalOldLevel = "";

function newTableRow(rowNum) {
    [].forEach.call(document.querySelectorAll('.mainTable'),
        function (e) {
            e.innerHTML += "<tr>" +
                "              <td id=T" + rowNum + " class='main" + rowNum + "' ondrop=drop(event,this) ondragover=allowDrop(event)>\n" +
                "              <div>\n" +
                "                <button type='submit' title='Add new topic' onclick=" + "openAddWindow('" + rowNum + "') class='addButton' >ADD</button>\n" +
                "              </div>\n" +
                "              </td>\n" +
                "          </tr>"
        });
}

function newTopic(name, tags, kind, gtk, description) {
    let showGtk, showDesc, tagsArray, tagField = "", tempTags = tags, showTags;
    if (tags.length > 0) {
        tags = tags.replaceAll(",", " ");
        tagsArray = tempTags.split(",");
        tagsArray.forEach(element => tagField += "#" + element + " ");
    }
    showGtk = "";
    if (gtk.length === 0) {
        showGtk = "none";
    }
    if (description.length === 0) {
        showDesc = "none";
    }
    if(tags.length === 0){
        showTags = "none";
    }
    let newName = name.replaceAll("_"," ");
    [].forEach.call(document.querySelectorAll('.main' + kind),
        function (e) {
            e.innerHTML = e.innerHTML +
                "<div class='topic " + tags + "'>" +
                "   <div class='top' draggable='true' id='topicDiv" + name + "' ondragstart=drag(event," + kind + ")>" +
                "       <button class='topicName' id='topicNameBtt' title='Go to post' onclick=openPostSite('" + kind + "','" + name + "')>" + newName + "</button>" +
                "       <div class='symbolDiv'>" + "" +
                "           <button class='tagButton' title='Show Tags' style='display: " + showTags + "' onclick=" + "changeTagFieldStatus('tag" + encodeURIComponent(name) + "')>#</button>" +
                "           <form method='POST'>" +
                "               <button class='editBtt' title='Edit Content' type='submit' value='" + kind + ",/T" + kind + "/" + newName + "' name='editTopic'><svg xmlns=\"http://www.w3.org/2000/svg\" height=\"1em\" viewBox=\"0 0 512 512\"><style>svg{fill:#ffffff}</style><path d=\"M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152V424c0 48.6 39.4 88 88 88H360c48.6 0 88-39.4 88-88V312c0-13.3-10.7-24-24-24s-24 10.7-24 24V424c0 22.1-17.9 40-40 40H88c-22.1 0-40-17.9-40-40V152c0-22.1 17.9-40 40-40H200c13.3 0 24-10.7 24-24s-10.7-24-24-24H88z\"/></svg></button>" +
                "               <button type='submit' title='Delete Topic' onclick=" + "openDeleteWindow('" + name + "," + kind + "') class='deleteBtt' >🗑</button>" +
                "           </form>" +
                "       </div>" +
                "   </div>" +
                "<div ><div style='display: none' class='tagTextField' id='tag" + name + "' >" + tagField + "</div><details open style='display: " + showDesc + "'><summary>Beschreibung</summary>" + description +
                    "</details><details style='display: " + showGtk + "'><summary>Sollte man wissen!</summary>" + gtk + "</details>" +
                "</div>" +
                "</div>";
        });
}

function allowDrop(ev) {
    ev.preventDefault();

}

function drag(ev, level) {
    ev.dataTransfer.setData("text", ev.target.id);
    let div = document.getElementById(ev.target.id);
    globalName = div.getElementsByClassName("topicName")[0].innerHTML;
    globalOldLevel = level;
}

function drop(ev, target) {
    if (target.id === "T" + globalOldLevel) {
        return;
    }
    openDragAndDropWindow(ev, target.id);

}

function finalDrop(ev) {
    ev.preventDefault();
    let data = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(data));
}

function openDragAndDropWindow(ev, newLevel) {

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

function openPostSite(level, name) {
    name = name.replaceAll("_"," ");
    window.open("topics/T" + level + "/" + name + "/anwendung.php", "_self");
}

function changeTagFieldStatus(id) {
    let e = document.getElementById(id)
    if (e.innerHTML.length > 0) {
        if (e.style.display === "none") {
            e.style.display = "";
        } else (e.style.display = "none")
    }

}

function openEditWindow(name, gtK, tags, desc, level) {
    [].forEach.call(document.querySelectorAll('body'),
        function (e) {
            e.innerHTML = e.innerHTML +
                "<div class='addWindow'><div class='addForm'><form style='width: 100%' method='POST'>" +
                "Name<br><input class='textFieldAddW' type='text' name='topicName' value='" + name + "'><br>" +
                "Dependent topics<br><textarea class='textFieldAddW' name='GoodToKnow' placeholder='These are other topics the Students should know to understand this topic!'>" + gtK + "</textarea><br>" +
                "Tags<br><textarea class='textFieldAddW' name='topicTags' placeholder='Please seperate with commas!'>" + tags + "</textarea><br>" +
                "Description<br><textarea class='textFieldAddW' name='topicDescription'>" + desc + "</textarea><br>" +
                "<button class='finishAddBtt' type='submit' name='Add' value='" + level + "'>Confirm</button>" +
                "<button class='cancelBtt' type='submit' name='close'>Cancel</button><textarea name='oldName' style='display: none'>" + name + "</textarea></form></div></div>";
        });
}

function openAddWindow(level) {
    [].forEach.call(document.querySelectorAll('body'),
        function (e) {
            e.innerHTML = e.innerHTML +
                "<div class='addWindow'><div class='addForm'><form style='width: 100%' method='POST'>" +
                "Name<br><input class='textFieldAddW' type='text' name='topicName' required><br>" +
                "Dependent topics<br><textarea class='textFieldAddW' name='GoodToKnow' placeholder='These are other topics the Students should know to understand this topic!'></textarea><br>" +
                "Tags<br><textarea class='textFieldAddW' name='topicTags' placeholder='Please seperate with commas!(i.e. Algebra,Vectors)'></textarea><br>" +
                "Description<br><textarea class='textFieldAddW' name='topicDescription'></textarea><br>" +
                "<button class='finishAddBtt' type='submit' name='Add' value='" + level + "'>Add Topic</button>" +
                "<button class='cancelBtt' type='submit' name='close'>Cancel</button></form></div></div>";
        });
}

function openDeleteWindow(name) {
    name = name.split(",");
    name[0] = name[0].replaceAll("_"," ");
    [].forEach.call(document.querySelectorAll('body'),
        function (e) {
            e.innerHTML = e.innerHTML + "<div class='deleteWindow'><p>Do you really want to delete the folder <b>'" + name[0] + "'</b>?</p><form method='POST' class='deleteForm'>" +
                "<button type='submit' class='yes' name='deleteTopic' value='" + name + "'>Yes</button>" +
                "<button type='submit' name='close' class='no'>No</button></form></div>";
        });
}

function searchTopic() {
    var input, filter, a, txtValue, tags, divs, tagfilter, tagtext, text, tagbool = false;
    input = document.getElementById('searchTopic');
    tags = document.getElementById('searchTag');
    if (tags.value != null) {
        tagbool = true;
        tagfilter = tags.value.toUpperCase();
    }
    filter = input.value.toUpperCase();
    divs = document.getElementsByClassName("topic");
    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < divs.length; i++) {
        a = divs[i].getElementsByTagName("button")[0];
        tagtext = divs[i].className.substring(5).toUpperCase();
        
        text = a.innerText;
        txtValue = text.toUpperCase();
        if (txtValue.indexOf(filter) > -1) {
            if (tagtext.indexOf(tagfilter) > -1 && tagbool) {
                divs[i].style.display = "";
            }
        } else {
            divs[i].style.display = "none";
        }
    }
}

function searchTag() {
    var input, filter, a, txtValue, text, topicbool = false;
    input = document.getElementById('searchTag');
    topics = document.getElementById('searchTopic');
    if (topics.value != null) {
        topicbool = true;
        topicfilter = topics.value.toUpperCase();
    }
    filter = input.value.toUpperCase();
    divs = document.getElementsByClassName("topic");
   
    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < divs.length; i++) {
        a = divs[i].getElementsByTagName("button")[0].innerText.toUpperCase();
        text = divs[i].className.substring(5);
        txtValue = text.toUpperCase();
        if (txtValue.indexOf(filter) > -1) {
            if (a.indexOf(topicfilter) > -1 && topicbool) {
                divs[i].style.display = "";
            }
        } else {
            divs[i].style.display = "none";
        }
    }
}

//<a href="+"javascript:searchTopicWithLink('p')>test</a>
