<div class="row justify-content-center">
<div class="col-md-8">
<p>Send message to Staff</p>
<form method=post name=message action='<?php echo URLROOT; ?>/contactstaff/submit'>
<div class="form-group">

<div class="form-group">
	<label for="name">Subject: </label>
	<input id="name" type="text" class="form-control" name="sub" minlength="3" maxlength="75" required autofocus>
</div>
<div class="form-group">
    <label for="msg">Message: </label>
    <textarea class="form-control" id="msg" name="msg" rows="3"></textarea>
</div>
<div class="form-group">
	<button type="submit" class="btn btn-warning btn-block"><?php echo Lang::T("Submit"); ?></button>
</div>

</div>
</form>
</div>
</div>