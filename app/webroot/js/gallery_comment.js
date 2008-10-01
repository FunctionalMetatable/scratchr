function hideComment(id){
	var elementID = id.toString();
	var target = document.getElementById(elementID);
	target.style.innerHTML = 'none';
	return true;
}

function showReplyBox(comment_id, showReplies, toggle) {
	var element_name = 'reply_to_' + comment_id;
	var popup = document.getElementById(element_name);
	var frame = document.getElementById('gallery_comment_reply_frame_' + comment_id);
	var target = document.getElementById(comment_id);
	var link = document.getElementById('reply_hide_link_' + comment_id);
	var topframe = document.getElementById('reply_to_' + comment_id);
	var linkb_name = 'view_hide_link_' + comment_id;
	var linkb = document.getElementById(linkb_name);
	var list_name = 'reply_list_' + comment_id;
	var list = document.getElementById(list_name);
	var text_input = document.getElementById('gallery_comment_reply_input_' + comment_id);
	var view_hide_frame = document.getElementById('view_hide_link_frame_' + comment_id);
	
	if (popup.style.display == 'block') {
		popup.style.display = 'none';
		frame.style.display = 'none';
		topframe.style.display = 'none';
		if (link != null) {
			link.innerHTML = 'reply';
		}
	} else {
		popup.style.display = 'block';
		frame.style.display = 'block';
		topframe.style.display = 'block';
		if (link != null) {
			link.innerHTML = 'hide';
		}
	}
	
	if (showReplies) {
		list.style.display = 'block';
		linkb.innerHTML = 'hide replies';
		var button = document.getElementById('gallery_comment_reply_submit_' + comment_id);
		text_input.value = "";
		button.disabled = false;
		
		if (view_hide_frame.style.display != 'inline') {
			view_hide_frame.style.display = 'inline'
		}
	}
}

function showReplyList(comment_id) {
	var link_name = 'view_hide_link_' + comment_id;
	var link = document.getElementById(link_name);
	var list_name = 'reply_list_' + comment_id;
	var list = document.getElementById(list_name);
	var display = list.style.display;
	
	if (list.style.display == 'block') {
		list.style.display = 'none';
		link.innerHTML = 'view all replies';
	} else {
		list.style.display = 'block';
		link.innerHTML = 'hide replies';
	}
}

function hideReplyList(comment_id) {
	var link_name = 'view_hide_link_' + comment_id;
	var link = document.getElementById(link_name)
	var list_name = 'reply_list_' + comment_id;
	var list = document.getElementById(list_name);
	list.style.display = 'none';
	link.innerHTML = 'view replies';
}

function hideReplyBox(comment_id) { 
	var element_name = 'reply_to_' + comment_id;
	var popup = document.getElementById(element_name);
	var frame = document.getElementById('gallery_comment_reply_frame_' + comment_id);
	var target = document.getElementById(comment_id);
	var link = document.getElementById('reply_hide_link_' + comment_id);
	
	popup.style.display = 'none';
	frame.style.display = 'none';
	topframe.style.display = 'none';
	link.innerHTML = 'reply';
}

function enableReply(comment_id) {
	var button = document.getElementById('gallery_comment_reply_submit_' + comment_id);
	button.disabled = false;
}
function disableReply(comment_id) {
	var button = document.getElementById('gallery_comment_reply_submit_' + comment_id);
	button.disabled = true;
}

function getAbsoluteY( oElement )
{
	var iReturnValue = 0;
		while( oElement != null ) {
		iReturnValue += oElement.offsetTop;
		oElement = oElement.offsetParent;
	}
	return iReturnValue;
}

function getAbsoluteX( oElement )
{
	var iReturnValue = 0;
		while( oElement != null ) {
		iReturnValue += oElement.offsetLeft;
		oElement = oElement.offsetParent;
	}
	return iReturnValue;
}