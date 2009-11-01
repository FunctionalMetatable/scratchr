/*
 * Slightly Thickerbox 1.7
 * By Jason Levine (http://www.jasons-toolbox.com)
 * A modification of Thickbox by Cody Lindley (http://www.codylindley.com)
 * Under an Attribution, Share Alike License
 * Thickbox is built on top of the very light weight jquery library.
 */

//on page load call TB_init
jQuery(document).ready(TB_init);

var TB_NextObjToShow, TB_NextDirection, TB_WIDTH = 0, TB_HEIGHT = 0, TB_VisibleSelects, TB_WasOpen;

//add thickbox to href elements that have a class of .thickbox
function TB_init(){
	jQuery("a.thickbox").click(function(){
        //var t = this.title || this.innerHTML || this.href;
		//TB_show(t,this.href);
		TB_ShowObj(this);
		this.blur();
		return false;
	});
	TB_WasOpen = false;
}

function TB_getPrevObj(ThickObj) {
	var PrevObj = null;
	var url = ThickObj.href;
	var thickgroup = ThickObj.rel;
	
	if (thickgroup != "") {	
		TB_ObjSet = jQuery("a.thickbox[@rel=" + thickgroup + "]")
		TB_ObjSize = TB_ObjSet.size();
		for (var TB_Counter = 0; TB_Counter < TB_ObjSize; TB_Counter++) {
			if (TB_ObjSet.get(TB_Counter) == url) {
				TB_Counter = TB_ObjSize + 1;  // Exit out of the loop
			} else {
				PrevObj = TB_ObjSet.get(TB_Counter);
			}
		}
	}
	
	return PrevObj;
}

function TB_getNextObj(ThickObj) {
	var NextObj = null;
	var url = ThickObj.href;
	var thickgroup = ThickObj.rel;
	var FoundThickObj = 0;
	
	if (thickgroup != "") {	
		TB_ObjSet = jQuery("a.thickbox[@rel=" + thickgroup + "]")
		TB_ObjSize = TB_ObjSet.size();
		for (var TB_Counter = 0; TB_Counter < TB_ObjSize; TB_Counter++) {
			if (TB_ObjSet.get(TB_Counter) == url) {
				FoundThickObj = 1;
			} else {
				if (FoundThickObj == 1) {
					NextObj = TB_ObjSet.get(TB_Counter);
					TB_Counter = TB_ObjSize + 1;  // Exit out of the loop
				}
			}
		}
	}
	
	return NextObj;
}


function TB_ShowObj(ThickObj) {
	var caption, url, thickgroup;
	try {
		caption = ThickObj.title || ThickObj.name || "";
		url = ThickObj.href;
		thickgroup = ThickObj.rel;
	
		if (document.getElementById("TB_overlay") == null) {
			jQuery("body").append("<div id='TB_overlay'></div><div id='TB_window'></div>");
			jQuery("#TB_overlay").click(TB_remove);
		}
		jQuery(window).resize(TB_position);
		jQuery(window).scroll(TB_position);
		
		TB_PrevObj = TB_getPrevObj(ThickObj);
		if (TB_PrevObj != null) {
			TB_PrevHTML = "<div id='TB_prev'><a href='#'>&lt;&lt; Prev</a></div>";
		} else {
			TB_PrevHTML = "";				
		}
		TB_NextObj = TB_getNextObj(ThickObj);
		if (TB_NextObj != null) {
			TB_NextHTML = "<div id='TB_next'><a href='#'>Next &gt;&gt;</a></div>";
		} else {
			TB_NextHTML = "";				
		}
 		
		//jQuery("#TB_overlay").show();
		jQuery("body").append("<div id='TB_load'><div id='TB_loadContent'><img src='images/circle_animation.gif' /></div></div>");
		var urlString = /\.jpg|\.jpeg|\.png|\.gif|\.mpg|\.mpeg|\.avi|\.html|\.htm|\.php|\.cfm|\.asp|\.aspx|\.jsp|\.jst|\.rb|\.txt/g;
		var urlType = url.toLowerCase().match(urlString) + '';
		switch (urlType) {
			case ".jpg":
			case ".jpeg":
			case ".png":
			case ".gif":
				var imgPreloader = new Image();
				imgPreloader.onload = function(){
					// Resizing large images added by Christian Montoya
					var pagesize = getPageSize();
					var x = pagesize[0] - 150;
					var y = pagesize[1] - 150;
					var imageWidth = imgPreloader.width;
					var imageHeight = imgPreloader.height;
					if (imageWidth > x) {
						imageHeight = imageHeight * (x / imageWidth); 
						imageWidth = x; 
						if (imageHeight > y) { 
							imageWidth = imageWidth * (y / imageHeight); 
							imageHeight = y; 
						}
					} else {
						if (imageHeight > y) { 
							imageWidth = imageWidth * (y / imageHeight); 
							imageHeight = y; 
							if (imageWidth > x) { 
								imageHeight = imageHeight * (x / imageWidth); 
								imageWidth = x;
							}
						}
					}
					// End Resizing
					TB_WIDTH = imageWidth; // +60
					TB_HEIGHT = imageHeight; // +80
					jQuery("#TB_window").append("<div id='TB_caption'>"+caption+"</div><div id='TB_closeWindow'><a href='#' id='TB_closeWindowButton'>Close window</a></div><div id='TB_SecondLine'>" + TB_PrevHTML + TB_NextHTML + "</div><div id='TB_ImageDIV'><a href='' id='TB_ImageOff' title='Close'><img id='TB_Image' src='"+url+"' width='"+imageWidth+"' height='"+imageHeight+"' alt='"+caption+"' onload='TB_ShowBox();'/></a></div>");

					jQuery("#TB_closeWindowButton").click(TB_remove);
					if (!(TB_PrevHTML == "")) {
						jQuery("#TB_prev").click(function () {
							TB_HideBox("r", TB_PrevObj);
						});
					}
					if (!(TB_NextHTML == "")) {
						jQuery("#TB_next").click(function () {
							TB_HideBox("l", TB_NextObj);
						});
					}
					TB_position();
					jQuery("#TB_load").remove();
					jQuery("#TB_ImageOff").click(TB_remove);
				}
		  
				imgPreloader.src = url;
				break;
			case ".mpg":
			case ".mpeg":
			case ".avi":
				var queryString = url.replace(/^[^\?]+\??/,'');
				var params = parseQuery( queryString );
				TB_WIDTH = (params['width']*1); // +60
				TB_HEIGHT = (params['height']*1); // +80
				ajaxContentW = TB_WIDTH - 30;
				ajaxContentH = TB_HEIGHT - 45;
				jQuery("#TB_window").append("<div id='TB_caption'>"+caption+"</div><div id='TB_closeWindow'><a href='#' id='TB_closeWindowButton'>Close window</a></div><div id='TB_SecondLine'>" + TB_PrevHTML + TB_NextHTML + "</div><div id='TB_ImageDIV' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'><a href='' id='TB_ImageOff' title='Close'><embed id='TB_Movie' src='" + url + "' autostart='true'></embed></div>");

				jQuery("#TB_closeWindowButton").click(TB_remove);
				if (!(TB_PrevHTML == "")) {
					jQuery("#TB_prev").click(function () {
						TB_HideBox("r", TB_PrevObj);
					});
				}
				if (!(TB_NextHTML == "")) {
					jQuery("#TB_next").click(function () {
						TB_HideBox("l", TB_NextObj);
					});
				}
				TB_position();
				jQuery("#TB_load").remove();
				jQuery("#TB_ImageOff").click(TB_remove);
				TB_ShowBox();
				break;
			default:
				var queryString = url.replace(/^[^\?]+\??/,'');
				var params = parseQuery( queryString );
				
				TB_WIDTH = (params['width']*1) + 30; // +60
				TB_HEIGHT = (params['height']*1) + 40; // +80
				ajaxContentW = TB_WIDTH - 30;
				ajaxContentH = TB_HEIGHT - 45;
				jQuery("#TB_window").append(TB_PrevHTML + TB_NextHTML + "</div><div id='TB_ajaxContent' style='width:"+ajaxContentW+"px;height:"+ajaxContentH+"px;'></div>" + "<div id='TB_closeAjaxWindow'><a href='#' id='TB_closeWindowButton'>Close window</a></div><div id='TB_SecondLine'>");
				jQuery("#TB_closeWindowButton").click(TB_remove);
				jQuery("#TB_ajaxContent").load(url, function(){
					TB_position();
					jQuery("#TB_load").remove();
					if (!(TB_PrevHTML == "")) {
						jQuery("#TB_prev").click(function () {
							TB_HideBox("r", TB_PrevObj);
						});
					}
					if (!(TB_NextHTML == "")) {
						jQuery("#TB_next").click(function () {
							TB_HideBox("l", TB_NextObj);
						});
					}
					jQuery("#TB_loadContent").show();
					TB_ShowBox();
				});
				break;
		}
	} catch(e) {
		alert( e );
	}
}

function TB_ShowBox(Direction) {
	jQuery("#TB_overlay").show();
	TB_VisibleSelects = jQuery("select:visible");
	TB_VisibleSelects.toggle();
	if (jQuery().DropInLeft != undefined) {
		// Interface Elements for JQuery are included
		if (Direction == undefined) {
			if (TB_NextDirection == undefined) {
				Direction = "l";
			} else {
				Direction = TB_NextDirection;
			}
		}
		if (TB_WasOpen) {
			if (Direction.toLowerCase() == "r") {
				jQuery("#TB_window").DropInRight(250);
			} else {
				jQuery("#TB_window").DropInLeft(250);
			}
		} else {
			jQuery("#TB_window").Grow(150);
			TB_WasOpen = true;
		}
	} else {
		// Interface Elements for JQuery are not included
		jQuery("#TB_overlay").show();
		jQuery("#TB_window").slideDown("normal");
		TB_WasOpen = true;
	}
}

function TB_AnimationComplete() {
//		jQuery("#TB_overlay").show();
		jQuery("#TB_window").fxReset();
}

function TB_HideBox(Direction, LocalNextObjToShow) {
	TB_NextObjToShow = LocalNextObjToShow;
	if (jQuery().DropOutRight != undefined) {
		// Interface Elements for JQuery are included
		if (Direction == undefined) {
			Direction = "l";
		}
		TB_NextDirection = Direction;
		if (Direction.toLowerCase() == "r") {
			jQuery("#TB_window").DropOutLeft(250, function() {
				TB_HideBox_Part2();
			});
		} else {
			jQuery("#TB_window").DropOutRight(250, function() {
				TB_HideBox_Part2();
			});
		}
	} else {
		// Interface Elements for JQuery are not included
		jQuery("#TB_window").slideUp("slow");
		TB_HideBox_Part2();
	}
}

function TB_HideBox_Part2() {
	jQuery("#TB_load").remove();
	jQuery("#TB_window").remove();
	jQuery("body").append("<div id='TB_window'></div>");
	TB_ShowObj(TB_NextObjToShow);
}

//helper functions below

function TB_remove() {
    if (jQuery().Shrink != undefined) {
        // Interface Elements for JQuery are included
		//jQuery("#TB_window").Fold(500, 20, function() {
		//	jQuery('#TB_window,#TB_overlay').remove();
		//	jQuery("#TB_load").remove();
		//	TB_VisibleSelects.toggle();
		//	TB_WasOpen = false;
		//	return false;
		//});
		jQuery("#TB_window").Shrink(500, function() {
			jQuery('#TB_window,#TB_overlay').remove();
            jQuery("#TB_load").remove();
			TB_VisibleSelects.toggle();
			TB_WasOpen = false;
			return false;
		});
	} else {
		// Interface Elements for JQuery are not included
		jQuery("#TB_window").fadeOut("fast",function(){jQuery('#TB_window,#TB_overlay').remove();});
		jQuery("#TB_load").remove();
		TB_VisibleSelects.toggle();
		TB_WasOpen = false;
		return false;
	
	TB_WasOpen = false;
	alert(TB_WasOpen);
	}
}

function TB_position() {
	var pagesize = getPageSize();
  
  	if (window.innerHeight && window.scrollMaxY) {	
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		yScroll = document.body.offsetHeight;
  	}
	
	var arrayPageScroll = getPageScrollTop();
	
	jQuery("#TB_window").css({width:TB_WIDTH+"px",height:TB_HEIGHT+"px",
	left: ((pagesize[0] - TB_WIDTH)/2)+"px", top: (arrayPageScroll[1] + ((pagesize[1]-TB_HEIGHT)/2))+"px" });
	jQuery("#TB_overlay").css("height",yScroll +"px");

}

function parseQuery ( query ) {
   var Params = new Object ();
   if ( ! query ) return Params; // return empty object
   var Pairs = query.split(/[;&]/);
   for ( var i = 0; i < Pairs.length; i++ ) {
      var KeyVal = Pairs[i].split('=');
      if ( ! KeyVal || KeyVal.length != 2 ) continue;
      var key = unescape( KeyVal[0] );
      var val = unescape( KeyVal[1] );
      val = val.replace(/\+/g, ' ');
      Params[key] = val;
   }
   return Params;
}


function getPageScrollTop(){
	var yScrolltop;
	if (self.pageYOffset) {
		yScrolltop = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScrolltop = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScrolltop = document.body.scrollTop;
	}
	arrayPageScroll = new Array('',yScrolltop) 
	return arrayPageScroll;
}

function getPageSize(){
	var de = document.documentElement;
	var w = window.innerWidth || self.innerWidth || (de&&de.clientWidth) || document.body.clientWidth;
	var h = window.innerHeight || self.innerHeight || (de&&de.clientHeight) || document.body.clientHeight;
	
	arrayPageSize = new Array(w,h) 
	return arrayPageSize;
}