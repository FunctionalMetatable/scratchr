function showHelp(){
 if(!$('.popup').is(':visible')){
   $('.popup').empty();
   $('.popup').append('<img id="logo" src="/static/experimental/img/justscratch.png"><strong>Experimental Viewer</strong><img id="close" src="/static/experimental/img/close.png" onClick="closeHelp()">');
   $('.popup').append('<br>This viewer lets you see and play with all the parts of a Scratch project directly in your web browser.<br><br><strong>What can I do?</strong><br>You can run the project, and also experiment with changes to the scripts.<br><br><strong>Can I save?</strong><br>Currently, you cannot save any changes from this viewer.<br><br><strong>How do I use it?</strong><br>Click a sprite beneath the stage to see its scripts, costumes, and sounds.<br><br>&#x2022; Make changes to scripts and click to see them run.<br>&#x2022; Click << or a colored square to open the palette of available blocks.<br>&#x2022; Click the green flag to run.<br><br><strong>Why is it called experimental?</strong><br>This is a temporary prototype. It is part of preliminary experimenting to design future versions of Scratch online. <br><br><strong>Questions or comments?</strong><br>Please share any questions or suggestions about this experimental viewer on this <a href="#">Scratch forum</a>.');
   $('.popup').show();
 }
}

function closeHelp(){
 if($('.popup').is(':visible')){
   $('.popup').hide();
 }
}