function xloc_cleartext() {
    document.getElementById('search-address').value = "";
    return false;
}

//For the Password Hashing API
function toggle_password_api(target) {
    var d = document;
    var tag = d.getElementById(target);
    var tag2 = d.getElementById("showhide");

    if (tag2.innerHTML == 'Show') {
        tag.setAttribute('type', 'text');
        tag2.innerHTML = 'Hide';
    } else {
        tag.setAttribute('type', 'password');
        tag2.innerHTML = 'Show';
    }
}
