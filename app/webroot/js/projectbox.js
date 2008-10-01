//scrollToFit()
//scrolls the project page to accomodate for applet
function scrollToFit() {
	var arrayPageSize = getPageSize();
	var arrayPageScroll = getPageScroll();
	
	scroll(0, 400);
}

//
// getPageScroll()
// Returns array with x,y page scroll values.
// Core code from - quirksmode.org
//
function getPageScroll(){

	var yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
	}

	arrayPageScroll = new Array('',yScroll) 
	return arrayPageScroll;
}

//
// getPageSize()
// Returns array with page width, height and window width, height
// Core code from - quirksmode.org
// Edit for Firefox by pHaez
//
function getPageSize(){
	
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else { 
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){	
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}


	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}


//
// initLightbox()
// Function runs on window load, going through link tags looking for rel="lightbox".
// These links receive onclick events that enable the lightbox display for their targets.
// The function also inserts html markup at the top of the page which will be used as a
// container for the overlay pattern and the inline image.
//
function initProjectbox()
{
	initLinks();
	var objBody = document.getElementsByTagName("body").item(0);
	
	//var arrayPageSize = getPageSize();
	//var arrayPageScroll = getPageScroll();

	// create lightbox div, same note about styles as above
	var objHanbox = document.getElementById("gallerybox");
	if (objHanbox == null) {
	
	} else {
		objHanbox.setAttribute('id','gallerybox');
		objHanbox.style.display = 'none';
		objHanbox.style.zIndex = '5100';	
	}
}

function initErrorbox() {
// create lightbox div, same note about styles as above
	var objOverlay = document.getElementById("overlay");
	objOverlay.setAttribute('id','overlay');
	objOverlay.onclick = function () {hideHanbox(); return false;}
	objOverlay.style.display = 'none';
	objOverlay.style.position = 'absolute';
	objOverlay.style.top = '0';
	objOverlay.style.left = '0';
	objOverlay.style.zIndex = '90';
 	objOverlay.style.width = '100%';
	
	var objHanbox = document.getElementById("errorbox");
	if (objHanbox == null) {
	
	} else {
		objHanbox.setAttribute('id','errorbox');
		objHanbox.style.display = 'none';
		objHanbox.style.zIndex = '5100';	
		objHanbox.style.position = 'absolute';
	}
}

//
// showAddbox()
// Preloads images. Pleaces new image in lightbox then centers and displays.
//
function showErrorbox()
{
	var objErrorbox = document.getElementById('errorbox');
	var objOverlay = document.getElementById('overlay');
		
	var arrayPageSize = getPageSize();
	var arrayPageScroll = getPageScroll();
	
	// set height of Overlay to take up whole page and show
	objOverlay.style.height = (arrayPageSize[1] + 'px');
	objOverlay.style.display = 'block';
	
	objErrorbox.style.width = 480 + 'px';
	objErrorbox.style.height = 60 + 'px';
	
	// center lightbox and make sure that the top and left values are not negative
	// and the image placed outside the viewport
	var height = arrayPageSize[3]/2;
	var width = arrayPageSize[2]/2
	var lightboxTop = arrayPageScroll[1] + ((arrayPageSize[3] - 35 - height) / 2);
	var lightboxLeft = ((arrayPageSize[0] - 20 - width) / 2);
	
	objErrorbox.style.display = 'block';
	objErrorbox.style.top = (lightboxTop < 0) ? "0px" : lightboxTop + "px";
	objErrorbox.style.left = (lightboxLeft < 0) ? "0px" : lightboxLeft + "px";
	objErrorbox.style.width = width + 'px';
	objErrorbox.style.height = height + 'px';
	
	if (objErrorbox.style.display == 'none') {
		objErrorbox.style.display = 'block';
	} else {
		objErrorbox.style.display = 'none';
	}
}

function setErrorMessage(msg) {
	var objErrorbox = document.getElementById('errorbox');
	
	objErrorbox.style.innerHTML = msg;
}

function checkLocked(locked) {
	if (locked == 1) {
		alert("Invalid action - this project has been locked by its creator");
		return false;
	} else {
		return true;
	}
}

function checkUser(locked) {
	if (locked == 1) {
		alert("Invalid action - your user account has been locked");
		return false;
	} else {
		return true;
	}
}

function checkAll(elid, locked, userlocked) {
	return checkLogin(elid) && checkLocked(locked) && checkUser(userlocked);
}

//
// showAddbox()
// Preloads images. Pleaces new image in lightbox then centers and displays.
//
function showGallerybox()
{
	var objAddBox = document.getElementById('gallerybox');
	
	objAddBox.style.width = 480 + 'px';
	objAddBox.style.height = 150 + 'px';
	if (objAddBox.style.display == 'none') {
		objAddBox.style.display = 'block';
	} else {
		objAddBox.style.display = 'none';
	}
}

function initLinks() {
	var objAdd = document.getElementById('gallery_project_add');
	
	
	if (objAdd == null) {
	
	} else {
		objAdd.onclick = function () {showGallerybox(); return false;};
	}
}

function hideGallerybox() {
	objAddBox = document.getElementById('gallerybox');
	
	objAddBox.style.display = 'none';
}

//
// addLoadEvent()
// Adds event to window.onload without overwriting currently assigned onload functions.
// Function found at Simon Willison's weblog - http://simon.incutio.com/
//
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