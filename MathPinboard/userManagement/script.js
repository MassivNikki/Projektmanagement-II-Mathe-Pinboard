function rebuildDatabases() {
    // Use AJAX to call the PHP script for rebuilding databases
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "rebuild.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText);
        }
    };
    xhr.send();
}

function deleteDatabases() {
    // Use AJAX to call the PHP script for deleting databases
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "delete.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText);
        }
    };
    xhr.send();
}
