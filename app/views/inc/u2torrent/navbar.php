<nav class="navbar navbar-expand-lg navbarone">
        <!-- Start Infobar -->

            <?php
if (!$_SESSION['loggedin'] == true) {
    //echo "&nbsp&nbsp<a href='".URLROOT."/login'><font color='#fff'>" . Lang::T("LOGIN") . "</font></a>&nbsp//&nbsp<a href='".URLROOT."/faq'\><font color='#fff'>" . Lang::T("FAQ") . "</font></a>&nbsp//&nbsp<a href='".URLROOT."/rules'\><font color='#fff'>" . Lang::T("RULES") . "</font></a><b>";
	echo "&nbsp<b>";
} else {

    $avatar = htmlspecialchars($_SESSION["avatar"]);
    if (!$avatar) {
        $avatar = "assets/images/default_avatar.png";
    }

    $userdownloaded = mksize($_SESSION["downloaded"]);
    $useruploaded = mksize($_SESSION["uploaded"]);
    $privacylevel = Lang::T($_SESSION["privacy"]);
    $countslot = DB::run("SELECT DISTINCT torrent FROM peers WHERE userid =?  AND seeder=?", [$_SESSION['id'], 'yes']);
    $maxslotdownload = $countslot->rowCount();
    $slots = number_format($_SESSION["maxslots"]) . "/" . number_format($maxslotdownload);

    if ($_SESSION["uploaded"] > 0 && $_SESSION["downloaded"] == 0) {
        $userratio = '<span class="label label-success pull-right">Inf.</span>';
    } elseif ($_SESSION["downloaded"] > 0) {
        $userratio = '<span class="label label-info pull-right">' . number_format($_SESSION["uploaded"] / $_SESSION["downloaded"] . '</span>', 2);
    } else {
        $userratio = '<span class="label label-info pull-right">---</span>';
    }

    print(Lang::T("HI") . " &nbsp<a class='nav-top' href='".URLROOT."/profile?id=$_SESSION[id]'>" . Users::coloredname($_SESSION["username"]) . "</a>");
    // call controller/method
    echo "&nbsp;//&nbsp;<a class='nav-top' href='".URLROOT."/logout'><font color='#fff'>" . Lang::T("LOGOUT") . "</font></a>";
    if ($_SESSION["control_panel"] == "yes") {
        print("&nbsp;//&nbsp;<a class='nav-top' href='".URLROOT."/admincp'><font color='#fff'>" . Lang::T("STAFFCP") . "</font></a>");
    }

    // check for new pm's
    global $pdo;
    $arr = DB::run("SELECT * FROM messages WHERE receiver=" . $_SESSION["id"] . " and unread='yes' AND location IN ('in','both')")->fetchAll();
    $unreadmail = count($arr);
    if ($unreadmail !== 0) {
        print("&nbsp//&nbsp<a class='nav-top' href='".URLROOT."/messages/inbox'><b><font color='#fff'>$unreadmail</font> " . Lang::N("NEWPM", $unreadmail) . "</b></a>");
    } else {
        print("&nbsp//&nbsp<a class='nav-top' href='".URLROOT."/messages'><font color='#fff'>" . Lang::T("YOUR_MESSAGES") . "</font></a>");
    }
    //end check for pm's

    print("!&nbsp;//&nbsp;
          <img src='".URLROOT."/assets/images/seed.gif' border='none' height='20' width='20' alt='Downloaded' title='Downloaded'><font color='#FFCC66'><b>$userdownloaded</b></font>&nbsp;//&nbsp;
          <img src='".URLROOT."/assets/images/up.gif' border='none' height='20' width='20' alt='Uploaded' title='Uploaded'> <font color='#33CCCC'><b>$useruploaded</b></font>&nbsp;//&nbsp;
          <img src='".URLROOT."/assets/images/button_online.png' border='none' height='20' width='20' alt='Ratio' title='Ratio'> (<b><font color='#FFF'>$userratio</font></b>)&nbsp;//&nbsp;
          Used Slots:&nbsp;<font color=#00cc00>$slots</font></a>&nbsp;//&nbsp;Bonus points:&nbsp;Bonus &nbsp;<a class='nav-top' href='". URLROOT ."/bonus' title='Bonus Points'><font color=#00cc00>$_SESSION[seedbonus]</font></a>&nbsp;//&nbsp;Donated: <a class='nav-top' href='". URLROOT ."/donate' title='Donated Amount'><font color=gold>&nbsp;$_SESSION[donated]</font></a>&nbsp;");
//////connectable yes or know////////
    if ($_SESSION["view_torrents"] == "yes") {
        $activeseed = get_row_count("peers", "WHERE userid = '$_SESSION[id]' AND seeder = 'yes'");
        $activeleech = get_row_count("peers", "WHERE userid = '$_SESSION[id]' AND seeder = 'no'");
        $stmt = DB::run("SELECT connectable FROM peers WHERE userid=? LIMIT 1", [$_SESSION['id']]);
        $connect = $stmt->fetchColumn();
        if ($connect == 'yes') {
            $connectable = "<b><font color=\"#00FF00\">YES</font></b>";
        } elseif ($connect == 'no') {
            $connectable = "<b><font color=\"FFCCFF\">NO</font></b>";
        } else {
            $connectable = "<b><font color=\"99CCFF\">Check Settings</font></b>";
        }
    }

    print("&nbsp;<font color=#fff>(<i>Seeding:</i></font>&nbsp; <a class='nav-top' href=\"javascript:popout(0)\"onclick=\"window.open('".URLROOT."/peers/popoutseed?id=" . $_SESSION["id"] . "','Seeding','width=350,height=350,scrollbars=yes')\" title='You are Seeding'><font color='#EB7F27'><b>" . $activeseed . "</b></font></a>&nbsp;");
    print("<font color=#fff><i>Leeching:</i> </font>&nbsp;<a class='nav-top' href=\"javascript:popout(0)\"onclick=\"window.open('".URLROOT."/peers/popoutleech?id=" . $_SESSION["id"] . "','Seeding','width=350,height=350,scrollbars=yes')\" title='You are Leeching'><font color='#EB7F27'><b>" . $activeleech . "</b></font></a>&nbsp;");
    print("<font color=#fff><i>Connected:</i></font>&nbsp; " . $connectable . ")");
//////connectable yes or know end of mod////////

}
?>

        <!-- End Infobar -->
        </div>
    </nav>
    <!-- END HEADER -->
    <!-- START NAVIGATION -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="<?php echo URLROOT; ?>/home">
            <img class="d-none d-sm-block" src="<?php echo URLROOT; ?>/assets/images/logo.png"><!-- Image to show on screens from small to extra large -->
            <img class="d-sm-none" src="<?php echo URLROOT; ?>/assets/images/logosmall.png"><!-- Image to show on extra small screen (mobile portrait) -->
        </a> 
      <?php if ($_SESSION['loggedin'] == true) {?>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav">
	  <li class="nav-item dropdown">
	  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Your Home</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/profile?id=<?php echo $_SESSION["id"]; ?>"><?php echo Lang::T("PROFILE"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT ?>/messages/inbox"><?php echo Lang::T("YOUR_MESSAGES"); ?></a>
            <a class="dropdown-item" href="<?php echo URLROOT ?>/peers/seeding?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("YOUR_TORRENTS"); ?></a>
            <a class="dropdown-item" href="<?php echo URLROOT ?>/friends?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("FRIENDS"); ?></a>
            <a class="dropdown-item" href="<?php echo URLROOT ?>/bonus"><?php echo Lang::T("SEEDING_BONUS"); ?></a> <!-- Check the link! -->
            <a class="dropdown-item" href="<?php echo URLROOT ?>/invite"><?php echo Lang::T("INVITES"); ?></a> <!-- Check the link! -->
		</div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Torrents</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
			<a class="dropdown-item" href="<?php echo URLROOT ?>/search/browse"><?php echo Lang::T("BROWSE_TORRENTS"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT ?>/upload"><?php echo Lang::T("UPLOAD_TORRENT"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT ?>/search"><?php echo Lang::T("SEARCH_TORRENTS"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT ?>/request"><?php echo Lang::T("MAKE_REQUEST"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT ?>/search/today"><?php echo Lang::T("TODAYS_TORRENTS"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT ?>/seed/needseed"><?php echo Lang::T("TORRENT_NEED_SEED"); ?></a>
        </div>
      </li>
	  <li class="nav-item dropdown">
	  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo Lang::T("FORUMS"); ?></a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/forums"><?php echo Lang::T("FORUMS"); ?></a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/forums/viewunread"><?php echo Lang::T("FORUM_NEW_POSTS"); ?></a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/forums/search"><?php echo Lang::T("SEARCH"); ?></a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/faq"><?php echo Lang::T("FORUM_FAQ"); ?></a>
		</div>
      </li>
	  <li class="nav-item dropdown">
	  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Contact Us</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/staff">Our Staff</a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/contactstaff"><?php echo Lang::T("Contact Staff"); ?></a>
		    <a class="dropdown-item" href="https://twitter.com/u2torrents"><?php echo Lang::T("TWITTER");?></a>
		    <a class="dropdown-item" href="https://www.facebook.com/pages/U2t/117634184954578"><?php echo Lang::T("FACEBOOK");?></a>
		    <a class="dropdown-item" href="http://u2radio.com/">U2Radio.com</a>
  </div>
      </li>
	  <?php if ($_SESSION["control_panel"] == "yes") {?>
	  <li class="nav-item dropdown">
	  <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
			<a href="<?php echo URLROOT; ?>/admincp" class="btn btn-warning"><?php echo Lang::T("STAFFCP"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT; ?>/users/profile?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("ACCOUNT_DETAILS"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT; ?>/users/details?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("YOUR_PROFILE"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT; ?>/messages/inbox"><?php echo Lang::T("YOUR_MESSAGES"); ?></a>
			<a class="dropdown-item" href="<?php echo URLROOT; ?>/torrents/create"><?php echo Lang::T("UPLOAD_TORRENT"); ?></a>
		</div>
      </li>
    <?php }?>
	  <li>
		<div class="search-container">
			<form method="get" action="<?php echo TTURL; ?>/torrents/search" class="form-inline">
				<div class="input-group-sm">
					<input type="text" name="search" class="form-control" value="<?php echo htmlspecialchars($_GET['search']); ?>" />
					<span class="input-group-btn">
						<button type="submit" class="btn btn-secondary btn-sm"/><?php echo Lang::T("SEARCH"); ?></button>
					</span>
				</div>
			</form>
		</div>
      </li>
    </ul>
  </div>
  <?php }?>
</nav>
    <!-- END NAVIGATION -->