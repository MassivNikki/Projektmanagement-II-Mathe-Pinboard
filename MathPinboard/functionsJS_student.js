function newTableRow(rowNum) {
    [].forEach.call(document.querySelectorAll('.mainTable'),
        function (e) {
            e.innerHTML += "<tr>" +
                "              <td class='main" + rowNum + "'>\n</td>\n" +
                "          </tr>"
        });
}

function newTopic(name, tags, kind, gtk, description) {
    let showGtk, showDesc, tagsArray, tagField = "", tempTags = tags;
    if (tags.length > 0) {
        tags = tags.replaceAll(",", " ");
        tagsArray = tempTags.split(",");
        //console.log(tagsArray);
        tagsArray.forEach(element => tagField += "#" + element + " ");
    }
    showGtk = "";
    if (gtk.length === 0) {
        showGtk = "none";
    }
    if (description === "") {
        showDesc = "none";
    }
    let newName = name.replaceAll("_"," ");
    [].forEach.call(document.querySelectorAll('.main' + kind),
        function (e) {
            e.innerHTML = e.innerHTML +
                "<div class='topic " + tags + "'>" +
                "   <div class='top' id='topicDiv" + name + "'>" +
                "       <button class='topicName' id='topicNameBtt' title='Go to post' onclick=" + "openPostSite('" + kind + "','" + name + "')>" + newName + "</button>" +
                "           <button class='tagButton' title='Show Tags' onclick=" + "changeTagFieldStatus('tag" + name + "')>#</button>" +
                    "</div>" +
                "<div ><div style='display: none' class='tagTextField' id='tag" + name + "' >" + tagField + "</div><details open style='display: " + showDesc + "'><summary>Beschreibung</summary>" + description +
                "</details><details style='display: " + showGtk + "'><summary>Sollte man wissen!</summary>" + gtk + "</details>" +
                "</div>" +
                "</div>";
        });
}

function openPostSite(level, name) {
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

function searchTopic(){
    var input, filter, a, txtValue, tags, divs, tagfilter, tagtext, text, tagbool = false;
    input = document.getElementById('searchTopic');
    tags = document.getElementById('searchTag');
    if (tags.value != null) {
        tagbool = true;
        tagfilter = tags.value.toUpperCase();
    }
    filter = input.value.toUpperCase();
    divs = document.getElementsByClassName("topic");
    //console.log(filter);
    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < divs.length; i++) {
        a = divs[i].getElementsByTagName("button")[0];
        tagtext = divs[i].className.substring(5).toUpperCase();
        //console.log(divs[i]);
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
    //console.log(divs);
    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < divs.length; i++) {
        //console.log(divs[i]);
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