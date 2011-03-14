<?php
        $head->register_raw('<script type="text/javascript" src="/evercookie/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="/evercookie/swfobject-2.2.min.js"></script>
        <script type="text/javascript" src="/evercookie/evercookie.js"></script>');
?>
<script>

var ec = new evercookie();

ecVal = ""

function actOnCookie(value)
{
        ecVal = value;
}

function redir()
{
    window.location = "/bannedAccountWarn/" + ecVal;
}

ec.get("user", actOnCookie);
setTimeout("redir()", 3000);

</script>
<div class="fullcontent" id="repContent">
<p>
        <?php
                // we don't want this to be seen ever
                // unfortunately evercookie (or flash cookies for that matter) does not work without javascript
                // so we'll just allow the user to proceed to signup anyway
                ___("Welcome to Scratch!  Please wait...");
        ?>
        <noscript>
                <br />Please continue:  <a href="<?php echo $html->url('/signup')?>" ><span class='button2'><?php ___('Create account')?></span></a>
        </noscript>
</p>
</div>