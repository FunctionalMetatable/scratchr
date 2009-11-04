//the div which contains the login form
var toggled = false;

function showLogin() {
    if(toggled) { //visible
        $('#logincontainer').fadeOut('medium', null)
        toggled = false;
    }
    else {
        $('#logincontainer').fadeIn('slow',
                function() { $('#UserInput').focus() } );
        toggled = true;
    }
    return false;
}