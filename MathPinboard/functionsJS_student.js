//this method adds a new Row to the Topic table based of the folders in xampp every time it is called

function newTableRow(rowNum) {
    [].forEach.call(document.querySelectorAll('.mainTable'),
        function (e) {
            e.innerHTML += "<tr>" +
                "              <td class='main" + rowNum + "'>\n</td>\n" +
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
        tagsArray.forEach(element => tagField += "#" + element + " ");
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
    if (description === "") {
        showDesc = "none";
    }
    if(tags.length === 0){
        showTags = "none";
    }else{
        let tempTopics = tagField.split("#");
        //every listed tag will get a function, so if clicked, the tag will be searched
        tempTopics.forEach((element,index) => { tempTopics[index] = "<b class=\"topicLink\" onclick=\"searchTagsViaLink('"+element+"')\">"+ element + "</b>"});
        tagField = tempTopics.join('#');
    }
    //for saving purposes the name gets an underscore for space and here it will be reverted for better display
    let newName = name.replaceAll("_"," ");
    //all elements that match the search criteria get selected and the new topic will be added to these rows(normally just one)
    [].forEach.call(document.querySelectorAll('.main' + kind),
        function (e) {
            e.innerHTML = e.innerHTML +
                "<div class='topic " + tags + "'>" +
                "   <div class='top' id='topicDiv" + name + "'>" +
                //the button with the topic name gets a function so with a click, the post site will be opened
                "       <button class='topicName' id='topicNameBtt' title='Go to post' onclick=openPostSite('" + kind + "','" + name + "')>" + newName + "</button>" +
                //the tag field is normally hidden, so with this button it will be shown
                "           <div class='symbolDiv'><button class='tagButton' title='Show Tags' style='display: " + showTags + "' onclick=" + "changeTagFieldStatus('tag" + name + "')>#</button></div>" +
                    "</div>" +
                //the text fields with the tags and topics which are good to know
                "<div ><div style='display: none' class='tagTextField' id='tag" + name + "' >" + tagField + "</div><details open style='display: " + showDesc + "'><summary>Beschreibung</summary>" + description +
                "</details><details style='display: " + showGtk + "'><summary>Sollte man wissen!</summary>" + gtk + "</details>" +
                "</div>" +
                "</div>";
        });
}

make

function openPostSite(level, name) {//opens the post site
    name = name.replaceAll("_"," ");
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