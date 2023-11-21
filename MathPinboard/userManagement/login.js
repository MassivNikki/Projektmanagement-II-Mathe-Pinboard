var loginModule = (function() {

    // Used to include the whole login-form. Needs a div with the correct ID to work 
    function includeHTML(file, containerId) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById(containerId).innerHTML = this.responseText;
            }
        };
        xhttp.open("GET", file, true);
        xhttp.send();

        makePopupLinkCorrectly();
    }

    function makePopupLinkCorrectly(){
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Add the correct baseUrl to the link
                //needed due to the pretty whacky directory stuff that happens
                var linkElement = document.getElementById('registerLink');
                var originalHref = linkElement.getAttribute('href');
                var newHref = baseUrl + originalHref;
                linkElement.setAttribute('href', newHref);
            }
        };
        xhttp.open("GET", baseUrl+'login-form.html', true);
        xhttp.send();
        
    }

    function openLoginPopup() {
        document.getElementById("loginOverlay").style.display = "flex";

    }

    function closeLoginPopup() {
        document.getElementById("loginOverlay").style.display = "none";

    }

    function handleLogin() {
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;

        loginUser(username, password, function(response) {
            document.getElementById("loginResult").innerHTML = response.message;
            if (response.success) {
                closeLoginPopup();
                alert("Login successful!");
            }
        });

        // Prevent form submission

        return false;
    }

    function loginUser(username, password, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", baseUrl+"login.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                callback(response);
            }
        };

        var formData = "username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password);
        xhr.send(formData);
    }

    function getLoginToken() {
        const cookies = document.cookie.split('; ');
        for (const cookie of cookies) {
            const [cookieName, cookieValue] = cookie.split('=');
            if (cookieName === "login_token") {
                return decodeURIComponent(cookieValue);
            }
        }
        return null;
    }

    let validatedTokenInfo = {
        isValid: false,
        username: null,
        role: null
    };
    
    function validateToken(token) {
    
        // Make a request to the server to check the session validity and retrieve username and role
        return fetch(baseUrl+'check_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                loginToken: token,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                validatedTokenInfo.isValid = true;
                validatedTokenInfo.username = data.username;
                validatedTokenInfo.role = data.role;
            } else {
                validatedTokenInfo.isValid = false;
                validatedTokenInfo.username = null;
                validatedTokenInfo.role = null;
                console.log("Login credentials Invalid");
            }
    
            return validatedTokenInfo.isValid;
        })
        .catch(error => {
            console.error('Error validating token:', error);
            console.log("Error validating token:");
            validatedTokenInfo.isValid = false;
            validatedTokenInfo.username = null;
            validatedTokenInfo.role = null;
            return false;
        });
    }

   

    function autoCheckToken() {
        if (!validatedTokenInfo.isValid) {
            // If isValid is false, call validateToken to check and update the status
            return validateToken(getLoginToken())
                .then(isValid => {
                    //if it isnt valid, force login
                    if (!isValid) {
                        openLoginPopup();
                    }
                });
        };
    }
    function forceCheckToken(){
            // bypasses the validity check, incase you need to be REALLY sure
            return validateToken(getLoginToken())
                .then(isValid => {
                    if (!isValid) {
                        openLoginPopup();
                    }
                });
    }

    function getRole() {
        forceCheckToken();
        return validatedTokenInfo.role;
    }

    function getUsername() {
        forceCheckToken();
        return validatedTokenInfo.username;
    }

    // Public API
    return {
        includeHTML: includeHTML,
        openLoginPopup: openLoginPopup,
        handleLogin: handleLogin,
        autoCheckToken: autoCheckToken,
        getUsername: getUsername,
        getRole: getRole
    };

})();
//honestly? barely works, most of the time i have to instruct the pages to checkToken but its worth a try
window.onload = loginModule.autoCheckToken();
// Include the login form HTML
loginModule.includeHTML(baseUrl+'login-form.html', 'loginPopupContainer');
