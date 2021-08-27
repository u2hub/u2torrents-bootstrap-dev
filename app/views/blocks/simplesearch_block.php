<?php
if ($_SESSION['loggedin'] || !Config::TT()['MEMBERSONLY']) {
    $_GET['search'] = $_GET['search'] ?? '';
    Style::block_begin(Lang::T("SEARCH"));
    ?>
        <!-- content -->
	<form method="get" action="<?php echo URLROOT; ?>/search" class="form-inline">
		<div class="input-group">
			<input type="text" name="keyword" class="form-control" value="<?php echo htmlspecialchars($_GET['keyword']); ?>" />
			<span class="input-group-btn">
				<button type="submit" class="btn ttbtn"/><?php echo Lang::T("SEARCH"); ?></button>
			</span>
		</div>
	</form>
	<?php
    Style::block_end();
}
?>