//ensure the loadOrder is proper
document.addEventListener("DOMContentLoaded", function() {
    // Use AJAX to check if tables exist
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "check_tables.php", true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (!response.table1 || !response.table2) {
                    alert("Tables do not exist. Click OK to rebuild.");
                    window.location.href = "rebuild.php";
                }
            } else {
                console.error("Error checking tables: " + xhr.status);
            }
        }
    };
    xhr.send();
});
