function showHelp(){
 if(!$('.popup').is(':visible')){
   $('.popup').empty();
   $('.popup').append('<img id="logo" src="/static/experimental/img/justscratch.png"><strong>Experimental Viewer</strong><img id="close" src="/static/experimental/img/close.png" onClick="closeHelp()">');
   $('.popup').append('<br>This Experimental Viewer lets you see and play with all parts of a Scratch project directly in your web browser.<br><br><strong>What can I do with the Experimental Viewer?</strong><br>You can run the project, and also experiment with changes to the scripts.<br><br><strong>Can I save?</strong><br>Currently, you cannot save any changes from this viewer.<br><br><strong>How do I use it?</strong><br>Click a sprite beneath the stage to see its scripts, costumes, and sounds.<br>&#x2022; Make changes to scripts and click to see them run.<br>&#x2022; Click &lt;&lt; or a colored square to open the palette of programming blocks.<br>&#x2022; Click the green flag to run.<br><br><strong>What are the technical requirements for the Experimental Viewer?</strong><br>You\'ll need the latest version of Adobe Flash Player, which you can download from: <a href="http://get.adobe.com/flashplayer/" target="_blank">http://get.adobe.com/flashplayer/</a>.<br><br><strong>What can I do if it doesn\'t fit on my screen?</strong><br>Try pressing Ctrl+ or Ctrl- to make the viewer larger or smaller.<br><br><strong>Why is it called experimental?</strong><br>This is a temporary prototype. It is part of preliminary experimenting to design future versions of Scratch online.<br><br><strong>What information will be collected?</strong><br>The Experimental Viewer records mouse clicks. If you choose to participate (opt in), you are agreeing to allow the Scratch team to record and analyze this information in order to understand usage patterns and inform improvements on the design. You can go to the <a href="/experimental" target="_blank">Experimental Viewer information page</a> and click \'Opt out\' at any time.<br><br><strong>Questions or comments?</strong><br>Please share any questions or suggestions about this Experimental Viewer on this <a href="#">Scratch forum</a>.');
   $('.popup').show();
 }
}

function closeHelp(){
 if($('.popup').is(':visible')){
   $('.popup').hide();
 }
}