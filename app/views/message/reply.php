
<?php include APPROOT.'/views/message/messagenavbar.php'; ?>

            <form name="form" action="<?php echo URLROOT; ?>/messages/submit" method="post">
            <input type="hidden" name="receiver" value="<?php echo $data['userid']; ?>" />
            <input type="hidden" name="subject" value="<?php echo $data['subject']; ?>" />
            <label>To</label>&nbsp;
                    <?php echo $data['username']; ?><br>

        <label for="template">Template:</label>&nbsp;
            <select name="template">
            <option name='0'>---</option>
            <?php  Helper::echotemplates(); ?>
            </select><br>
            <label>Subject</label>&nbsp;
                     <?php echo $data['subject']; ?><br>

        <?php print textbbcode("form", "body", $data['msg']);?>
        <center><button type="submit" class="btn-sm btn-warning" name="Update" value="create">Create</button>&nbsp;
    <label>Save Copy In Outbox</label>
    <input type="checkbox" name="save" checked='Checked'>&nbsp;
       </form>
        </center>