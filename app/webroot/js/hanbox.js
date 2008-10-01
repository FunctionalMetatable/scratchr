var loadingImage = '/img/loading.gif';	


function checkUser(locked) {
	if (locked == 1) {
		alert("Invalid action - your user account has been locked");
		return false;
	} else {
		return true;
	}
}

function checkAll(elid, locked) {
	return checkLogin(elid) && checkUser(locked);
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

function initHanbox()
{
	initLinks();
	var objOverlay = document.getElementById("overlay");
	var objLoadingDiv = document.getElementById("loadingDiv");
	var objBody = document.getElementsByTagName("body").item(0);
	var imgPreloader = new Image();
	
	// if loader image found, create link to hide lightbox and create loadingimage
	imgPreloader.onload=function(){
		
		var objLoadingImage = document.createElement("img");
		objLoadingImage.src = loadingImage;
		objLoadingImage.setAttribute('id','loadingImage');
		objLoadingImage.style.position = 'absolute';
		objLoadingImage.style.zIndex = '6000';
		objLoadingDiv.appendChild(objLoadingImage);


		imgPreloader.onload=function(){};	//	clear onLoad, as IE will flip out w/animated gifs

		return false;
	}

	imgPreloader.src = loadingImage;		

	// create overlay div and hardcode some functional styles (aesthetic styles are in CSS file)

	objOverlay.setAttribute('id','overlay');
	objOverlay.onclick = function () {hideHanbox(); return false;}
	objOverlay.style.display = 'none';
	objOverlay.style.position = 'absolute';
	objOverlay.style.top = '0';
	objOverlay.style.left = '0';
	objOverlay.style.zIndex = '90';
 	objOverlay.style.width = '100%';
	objBody.appendChild(objOverlay);
	
	//var arrayPageSize = getPageSize();
	//var arrayPageScroll = getPageScroll();

	// create lightbox div, same note about styles as above
	var objHanbox = document.getElementById("addbox");
	if (objHanbox == null) {
	
	} else {
		objHanbox.setAttribute('id','addbox');
		objHanbox.style.display = 'none';
		objHanbox.style.position = 'absolute';
		objHanbox.style.zIndex = '100';	
	}
	
	
	// create lightbox div, same note about styles as above
	var objHanbox = document.getElementById("removebox");
	if (objHanbox == null) {
	
	} else {
		objHanbox.setAttribute('id','removebox');
		objHanbox.style.display = 'none';
		objHanbox.style.position = 'absolute';
		objHanbox.style.zIndex = '100';	
	}
}


function preloadHanbox() {
	var arrayPageSize = getPageSize();
	var arrayPageScroll = getPageScroll();
	var objLoadingImage = document.getElementById("loadingImage");
	var objLoadingDiv = document.getElementById("loadingDiv");
	
	// center loadingImage if it exists
	if (objLoadingImage) {
	
	}
	
	setTimeout("showAddbox()", 4000);
}

function showAddbox()
{
	// prep objects
	var objOverlay = document.getElementById('overlay');
	var objAddBox = document.getElementById('addbox');
	var objLoadingImage = document.getElementById('loadingImage');
	var objLoadingDiv = document.getElementById('loadingDiv');
	
	var arrayPageSize = getPageSize();
	var arrayPageScroll = getPageScroll();

	// set height of Overlay to take up whole page and show
	objOverlay.style.height = (arrayPageSize[1] + 'px');
	objOverlay.style.display = 'block';
	
	// center lightbox and make sure that the top and left values are not negative
	// and the image placed outside the viewport
	var height = arrayPageSize[3]/2;
	var width = arrayPageSize[2]/2
	var lightboxTop = arrayPageScroll[1] + ((arrayPageSize[3] - 35 - height) / 2);
	var lightboxLeft = ((arrayPageSize[0] - 20 - width) / 2);
	
	objAddBox.style.display = 'block';
	objAddBox.style.top = (lightboxTop < 0) ? "0px" : lightboxTop + "px";
	objAddBox.style.left = (lightboxLeft < 0) ? "0px" : lightboxLeft + "px";
	objAddBox.style.width = width + 'px';
	objAddBox.style.height = height + 'px';
	objLoadingDiv.style.display = 'none';
}

//
// showRemovebox()
// Preloads images. Pleaces new image in lightbox then centers and displays.
//
function showRemovebox()
{
	// prep objects
	var objOverlay = document.getElementById('overlay');
	var objRemoveBox = document.getElementById('removebox');
	
	var arrayPageSize = getPageSize();
	var arrayPageScroll = getPageScroll();

	// set height of Overlay to take up whole page and show
	objOverlay.style.height = (arrayPageSize[1] + 'px');
	objOverlay.style.display = 'block';
	
	// center lightbox and make sure that the top and left values are not negative
	// and the image placed outside the viewport
	var height = arrayPageSize[3]/2;
	var width = arrayPageSize[2]/2
	var lightboxTop = arrayPageScroll[1] + ((arrayPageSize[3] - 35 - height) / 2);
	var lightboxLeft = ((arrayPageSize[0] - 20 - width) / 2);
	
	objRemoveBox.style.display = 'block';
	objRemoveBox.style.top = (lightboxTop < 0) ? "0px" : lightboxTop + "px";
	objRemoveBox.style.left = (lightboxLeft < 0) ? "0px" : lightboxLeft + "px";
	objRemoveBox.style.width = width + 'px';
	objRemoveBox.style.height = height + 'px';
}

function initLinks() {
	var objAdd = document.getElementById('addprojectmember');
	var objRemove = document.getElementById('removeprojectmember');
	var objAddButton = document.getElementById('addbutton');
	var objRemoveButton = document.getElementById('removebutton');
	
	
	if (objAdd == null) {
	
	} else {
		objAdd.onclick = function () {preloadHanbox(); return false;};
		objRemove.onclick = function () {showRemovebox(); return false;};
		objAddButton.onclick = function () {hideHanbox(); return false;};
		objRemoveButton.onclick = function () {hideHanbox(); return false;};
	}
}

function hideHanbox() {
	// get objects
	objOverlay = document.getElementById('overlay');
	objAddBox = document.getElementById('addbox');
	objRemoveBox = document.getElementById('removebox');

	// hide lightbox and overlay
	objOverlay.style.display = 'none';
	objAddBox.style.display = 'none';
	objRemoveBox.style.display = 'none';
	
	window.location.reload();
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

addLoadEvent(initHanbox);	// run initLightbox onLoad