function showOptions() {
	var objShow = document.getElementById('gallery_permission_advanced');
	var objS = document.getElementById('gallery_advanced_show');
	var objH = document.getElementById('gallery_advanced_hide');
	
	if (objShow == null) {
	
	} else {
		objS.style.display = 'none';
		objH.style.display = 'block';
		objShow.style.display = 'block';
	}
}

function hideOptions() {
	var objShow = document.getElementById('gallery_permission_advanced');
	var objS = document.getElementById('gallery_advanced_show');
	var objH = document.getElementById('gallery_advanced_hide');
	
	if (objShow == null) {
	
	} else {
		objS.style.display = 'block';
		objH.style.display = 'none';
		objShow.style.display = 'none';
	}
}

function initLinks() {
	var objShow = document.getElementById('gallery_advanced_show');
	var objHide = document.getElementById('gallery_advanced_hide');
	
	if (objShow == null) {
	
	} else {
		objShow.style.display = 'block';
		objHide.style.display = 'none';
		objShow.onclick = function () {showOptions(); return false;};
		objHide.onclick = function () {hideOptions(); return false;};
	}
}

function addLoadEvent(func)
{	
	var oldonload = window.onload;
	if (typeof window.onload != 'function'){
    	window.onload = func;
	} else {
		window.onload = function(){
		oldonload();
		func();
		}
	}
}

addLoadEvent(initLinks);