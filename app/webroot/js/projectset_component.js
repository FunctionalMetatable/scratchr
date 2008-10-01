var UT_RATING_IMG = '/img/star.gif';
var UT_RATING_IMG_HOVER = '/img/star_hover.gif';
var UT_RATING_IMG_HALF = '/img/star_half.gif';
var UT_RATING_IMG_BG = '/img/star_bg.gif';
var UT_RATING_IMG_REMOVED = '/img/star_removed.gif';

var PROJECT_VIEW_WIDTH = 480;
var PROJECT_VIEW_HEIGHT = 360;

var SCRATCHR_LOGO = '/img/scratchr-thumb.png';

// thumbnais: 133x100
// previewimages: 480x360;
// appletpreview: 480x360;

function projectSet(projectViewId, objectName, curProjectIndex) {
    this.objectName = objectName;
    this.projectViewerId = projectViewId;
    this.curProjectIndex = curProjectIndex;
    this.startTimer = null;
    // setting/clearing timeouts: setTimeout('function_call(\'param\')', int millisecs);
   
    /**
     * Action sequence:
     * 1. if (project page loading) check for cache version based on name
     * of this projectsetobject...somethinglikc PSBOX_PID;
     * 
     * 2. if no cached version create new passing it the following parameters:
     * the project_viewer_id, the created object itself (i.e. object name), the
     * relative src_paths/urls of the thumbnails, the relative src_paths/urls of the
     * corresponding preview_images, the current project/thumbnail index
     *
     * 3. still on page load, instead of loading the applet, load the current preview
     * image in the corresponding project_view_div (give option of loading applet
     * first or loading prev img and then having a button to run the project).
     *
     * 4. before loading project_setbox, add the scratch-thumb img file to the
     * beginning and end of the thumbnails array basically to serve as end points
     *
     * 5. on loading project_setbox, the current project thumbnail should now be
     * at position 1...based on zero indexed array.
     *
     * 6. ofcourse the next/prev buttons should point to their
     * corresponding functions in here passing in the two img elements
     * so that their srcs can be swaped and a new img can be place in 
     * the corresponding next/prev locations
     */

    /**
     * Project_setbox characteristics:
     * 1. can contain any number of thumbnails, not just two
     * 2. can click/hover next button to cycle through
     * 3. can click/hover prev button to cycle back
     * 4. can hover on thumbs to preview the larger counterpart somewhere else (optional)
     * 5. can hold project info (name, author)
     * 6. on page load, the setbox is centered w/ the current project/thumbnail (i.e. the
     * thumbnail of the currently viewed project is always at the center
     * 7. can click on a thumbnail to open up corresponding project page
     */

    // preloads image
    function preloadImages(imageCount, lookAhead) {
        if (lookAhead) {
            
        }
    }

    function setCurrentProject(index) {
        this.curProjIndex = index;
    }

    function replaceProjectViewDiv( , bool_toapplet) {
    }

    function swapImage(img_name, img_id, new_img) {
        if (document.getElementById) {
            document.getElementById(img_id).src = new_img;
        } else {
            document.images[img_name].src = new_img;
        }
    }

    function repopulate(thumbs, previews, curindex) {
    }
}

function UTRating(ratingElementId, maxStars, objectName, formName, ratingMessageId, componentSuffix, size)
{
	this.ratingElementId = ratingElementId;
	this.maxStars = maxStars;
	this.objectName = objectName;
	this.formName = formName;
	this.ratingMessageId = ratingMessageId
	this.componentSuffix = componentSuffix

	this.starTimer = null;
	this.starCount = 0;

	if(size=='S') {
		UT_RATING_IMG      = '/img/star_sm.gif'
		UT_RATING_IMG_HALF = '/img/star_sm_half.gif'
		UT_RATING_IMG_BG   = '/img/star_sm_bg.gif'
	}
	
	// pre-fetch image
	(new Image()).src = UT_RATING_IMG;
	(new Image()).src = UT_RATING_IMG_HALF;

	function showStars(starNum, skipMessageUpdate) {
		this.clearStarTimer();
		this.greyStars();
		this.colorStars(starNum);
		if(!skipMessageUpdate)
			this.setMessage(starNum);
	}

	function setMessage(starNum) {
		messages = new Array("Rate this project", "Poor", "Nothing special", "Worth playing with", "Pretty cool", "Awesome!");
		document.getElementById(this.ratingMessageId).innerHTML = messages[starNum];
	}

	function colorStars(starNum) {
		for (var i=0; i < starNum; i++)
			document.getElementById('star_'  + this.componentSuffix + "_" + (i+1)).src = UT_RATING_IMG;
	}

	function greyStars() {
		for (var i=0; i < this.maxStars; i++)
			if (i <= this.starCount)
				document.getElementById('star_' + this.componentSuffix + "_"  + (i+1)).src = UT_RATING_IMG_BG; // UT_RATING_IMG_REMOVED;
			else
				document.getElementById('star_' + this.componentSuffix + "_"  + (i+1)).src = UT_RATING_IMG_BG;
	}

	function setStars(starNum) {
		this.starCount = starNum;
		this.drawStars(starNum);
		document.forms[this.formName]['rating'].value = this.starCount;
		var ratingElementId = this.ratingElementId;
		//postForm(this.formName, true, function (req) { replaceDivContents(req, ratingElementId); });
	}

    function scratchrSetStars(starNum) {
        this.starCount = starNum;
        this.drawStars(starNum);
        document.forms[this.formName]['data[Vote][rating]'].value = this.starCount;
    }


	function drawStars(starNum, skipMessageUpdate) {
		this.starCount=starNum;
		this.showStars(starNum, skipMessageUpdate);
	}

	function clearStars() {
		this.starTimer = setTimeout(this.objectName + ".resetStars()", 300);
	}

	function resetStars() {
		this.clearStarTimer();
		if (this.starCount)
			this.drawStars(this.starCount);
		else
			this.greyStars();
		this.setMessage(0);
	}

	function clearStarTimer() {
		if (this.starTimer) {
			clearTimeout(this.starTimer);
			this.starTimer = null;
		}
	}

	this.clearStars = clearStars;
	this.clearStarTimer = clearStarTimer;
	this.greyStars = greyStars;
	this.colorStars = colorStars;
	this.resetStars = resetStars;
	this.setStars = setStars;
    this.scratchrSetStars = scratchrSetStars;
	this.drawStars = drawStars;
	this.showStars = showStars;
	this.setMessage = setMessage;

}


