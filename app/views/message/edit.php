<?php usermenu($_SESSION['id']);
 include APPROOT.'/views/message/messagenavbar.php'; ?><br>
<center><b><?php echo $data['subject']; ?></b></center><br>

<form name="form" action="<?php echo URLROOT; ?>/messages/update?id=<?php echo $data['id']; ?>" method="post"><br>
<?php print textbbcode("form", "msg", $data['msg']); ?>
<center><br>
<button type="submit" class="btn-sm ttbtn" name="Update" value="Update">Update</button><br>
</center>
</form>