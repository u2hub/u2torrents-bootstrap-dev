<center><b>Edit Message</b></center>
<div>
<form name='Form' method='post' action='<?php echo URLROOT; ?>/forums/editpost&amp;postid=<?php echo $data['postid']; ?>''>
<input type='hidden' name='returnto' value='<?php echo  htmlspecialchars($_SERVER["HTTP_REFERER"]); ?>' />
<div class='row justify-content-md-center'>
    <div class='col-md-10'>
        <?php
        textbbcode("Form", "body", $data['body']);
        ?>
    </div>
</div>
<center><button type='submit' class='btn btn-sm btn-warning'><?php echo Lang::T("SUBMIT"); ?></button></center>
</form>
</div>