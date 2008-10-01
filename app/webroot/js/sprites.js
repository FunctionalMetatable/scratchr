function centerImage() {
	var image = document.getElementById("sprite_image");
	var container = document.getElementById("sprite_image_container");
	
	var imageWidth = image.offsetWidth;
	var imageHeight = image.offsetHeight;
	
	var containerWidth = container.offsetWidth;
	var containerHeight = container.offsetHeight;
	
	if (imageWidth > containerWidth && imageHeight > containerHeight) {
		image.style.width = containerWidth;
		image.style.Height = containerHeight;
	} else {
		var widthDiff = containerWidth - imageWidth;
		var heightDiff = containerHeight - imageHeight;
		
		var top = heightDiff/2;
		var left = widthDiff/2;
		
		image.style.left = left - 10;
		image.style.top = top - 10;
	}
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

addLoadEvent(centerImage);