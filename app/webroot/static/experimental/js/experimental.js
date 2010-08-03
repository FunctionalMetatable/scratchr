function showHelp(){
 if(!$('.popup').is(':visible')){
   $('.popup').empty();
   $('.popup').append('<img id="logo" src="/static/experimental/img/justscratch.png"><strong>Experimental Viewer</strong><img id="close" src="close.png" onClick="closeHelp()">');
   $('.popup').append('<br>This viewer lets you see and play with all the parts of a Scratch project directly in your web browser.<br><br><strong>What can I do?</strong><br>You can run the project, and also experiment with changes to the scripts.<br><br><strong>Can I save?</strong><br>Currently, you cannot save any changes from this viewer.<br><br><strong>How do I use it?</strong><br>Click a sprite beneath the stage to see its scripts, costumes, and sounds.<br><br>� Make changes to scripts and click to see them run.<br>� Click << or a colored square to open the palette of available blocks.<br>� Click the green flag to run.<br><br><strong>Why is it called experimental?</strong><br>This is a temporary prototype. It is part of preliminary investigations for future versions of Scratch online. <br><br><strong>Questions or comments?</strong><br>Please send the Scratch team any questions or suggestions about this experimental viewer, below.');
   $('.popup').append('<form method="post" enctype="multipart/form-data" action="/contact/us/"> <div id="questionform"> <input id="topicinput" type="hidden" name="data[Page][cc_topic]" value=""> <p> Name:	<input type="text" name="data[Page][name]" size="25" /> </p> <p> Email:	 <input type="text" name="data[Page][email]" size="25" /> </p> <p> Subject:	 <input type="text" name="data[Page][subject]" size="25" /> </p> <p> Message:<br /><textarea name="data[Page][message]" cols="40" rows="5"></textarea> </p> <p> <input type="submit" value="Send email" /> </p> </div> </form>');
   $('.popup').show();
 }
}

function closeHelp(){
 if($('.popup').is(':visible')){
   $('.popup').hide();
 }
}