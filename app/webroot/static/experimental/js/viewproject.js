var jsReady = false;
var id, owner;

function setup(identity, obj){
	id = identity;
	owner = obj;
	jsReady = true;
	flash = document.getElementById('Scratch');	
	var flash  =document.getElementById('Scratch');	
	flash.setAttribute('class', 'maker');
}


//----------------------------------------
// External comm to Flash
//----------------------------------------

	
function isReady() {return jsReady;}
        
function sendToJavaScript(value) {
	var pp = bottomctrls.childNodes[1];
	pp.textContent = "ActionScript says: " + value ;
}
