<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="<?php echo URLROOT; ?>">
            <img class="d-none d-sm-block" src="<?php echo URLROOT; ?>/assets/images/logo.png"><!-- Image to show on screens from small to extra large -->
            <img class="d-sm-none" src="<?php echo URLROOT; ?>/assets/images/logosmall.png"><!-- Image to show on extra small screen (mobile portrait) -->
        </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <i class="fa fa-bars" style="color:#fff; font-size:28px;"></i>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <?php if (isset($_SESSION['id'])): ?>
    <ul class="navbar-nav mr-auto">

      <li class="nav-item active">
        <a class="nav-link" href="<?php echo URLROOT ?>">Home <span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Profile
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/profile?id=<?php echo $_SESSION["id"]; ?>"><?php echo Lang::T("PROFILE"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/messages?type=inbox"><?php echo Lang::T("YOUR_MESSAGES"); ?></a>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/peers/seeding?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("YOUR_TORRENTS"); ?></a>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/friends?id=<?php echo $_SESSION['id']; ?>"><?php echo Lang::T("FRIENDS"); ?></a>
        <a class="dropdown-item" href="<?php echo URLROOT ?>/bonus"><?php echo Lang::T("SEEDING_BONUS"); ?></a> <!-- Check the link! -->
        <a class="dropdown-item" href="<?php echo URLROOT ?>/invite"><?php echo Lang::T("INVITES"); ?></a> <!-- Check the link! -->
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#">Something else here</a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Torrents
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="<?php echo URLROOT ?>/search/browse"><?php echo Lang::T("BROWSE_TORRENTS"); ?></a>
			  <a class="dropdown-item" href="<?php echo URLROOT ?>/upload"><?php echo Lang::T("UPLOAD_TORRENT"); ?></a>
			  <a class="dropdown-item" href="<?php echo URLROOT ?>/search"><?php echo Lang::T("SEARCH_TORRENTS"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/request"><?php echo Lang::T("MAKE_REQUEST"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/search/today"><?php echo Lang::T("TODAYS_TORRENTS"); ?></a>
		  	<a class="dropdown-item" href="<?php echo URLROOT ?>/search/needseed"><?php echo Lang::T("TORRENT_NEED_SEED"); ?></a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Forums
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="<?php echo URLROOT ?>/forums"><?php echo Lang::T("FORUMS"); ?></a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/forums/viewunread"><?php echo Lang::T("FORUM_NEW_POSTS"); ?></a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/forums/search"><?php echo Lang::T("SEARCH"); ?></a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/faq"><?php echo Lang::T("FORUM_FAQ"); ?></a>
        </div>
      </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Contact
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/group/staff">Our Staff</a>
		    <a class="dropdown-item" href="<?php echo URLROOT ?>/contactstaff"><?php echo Lang::T("Contact Staff"); ?></a>
        </div>
      </li>

    </ul>
    <?php endif;?>

    <?php if (!$_SESSION['id']): ?>
    <ul class="navbar-nav mr-auto">
    <a href="<?php echo URLROOT ?>/forums"><b><?php echo Lang::T("FORUMS"); ?></b></a>&nbsp;&nbsp;
	  </ul>
    <?php endif;?>

    <ul class="navbar-nav navbar-right d-none d-sm-block">
    <?php if (isset($_SESSION['id'])): ?>
      Hi <a href="<?php echo URLROOT; ?>/profile?id=<?php echo $_SESSION['id']; ?>"><b><?php echo Users::coloredname($_SESSION['username']);?>,</b></a>
    <?php
    if ($_SESSION["uploaded"] > 0 && $_SESSION["downloaded"] == 0) {
        $userratio = 'Inf.';
    } elseif ($_SESSION["downloaded"] > 0) {
        $userratio = number_format($_SESSION["uploaded"] / $_SESSION["downloaded"], 2);
    } else {
        $userratio = '--';
    }
    $userdownloaded = mksize($_SESSION["downloaded"]);
    $useruploaded = mksize($_SESSION["uploaded"]);
    $privacylevel = Lang::T($_SESSION["privacy"]);
	$countslot = DB::run("SELECT DISTINCT torrent FROM peers WHERE userid =?  AND seeder=?", [$_SESSION['id'], 'yes']);
    $maxslotdownload = $countslot->rowCount();
    $slots = number_format($_SESSION["maxslots"]) . "/" . number_format($maxslotdownload);
      print("&nbsp;
        <i class='fa fa-upload' style='color:green' style='font-size:18px' alt='Uploaded' title='You have uploaded'></i>&nbsp;&nbsp;<b>$useruploaded</b>&nbsp;
        <i class='fa fa-download' style='color:indigo' style='font-size:18px' alt='Downloaded' title='You have downloaded'></i>&nbsp;&nbsp;<b>$userdownloaded</b>&nbsp;
        <i class='fa fa-cog fa-spin fa-3x fa-fw' style='font-size:18px' alt='Ratio' title='Your share ratio'></i>&nbsp;&nbsp;(<b>$userratio</b>)&nbsp;
        <i class='fa fa-smile-o' style='color:orange' style='font-size:16px' alt='Bonus' title='Your Bonus points'></i>&nbsp;&nbsp;<a href='". URLROOT ."/bonus' title='Bonus Points'><b>$_SESSION[seedbonus]</b></a>&nbsp;
		<i class='fa fa-paypal' style='color:gold' style='font-size:16px' alt='Donate' title='Your Donations'></i>&nbsp;&nbsp;<a href='". URLROOT ."/donate' title='Donated'><b>$_SESSION[donated]</b></a>&nbsp;
		<i class='fa fa-circle-o' style='color:blue' style='font-size:18px' alt='Slots' title='Used Slots'></i>&nbsp;&nbsp;(<b>$slots</b>)&nbsp;");
//////connectable yes or know////////
    if ($_SESSION["view_torrents"] == "yes") {
        $activeseed = get_row_count("peers", "WHERE userid = '$_SESSION[id]' AND seeder = 'yes'");
        $activeleech = get_row_count("peers", "WHERE userid = '$_SESSION[id]' AND seeder = 'no'"); 
        $stmt = DB::run("SELECT connectable FROM peers WHERE userid=? LIMIT 1", [$_SESSION['id']]);
        $connect = $stmt->fetchColumn();
        if ($connect == 'yes') {
            $connectable = "<b><font color=\"#00FF00\">YES</font></b>";
        } elseif ($connect == 'no') {
            $connectable = "<b><font color=\"#FFCCFF\">NO</font></b>";
        } else {
            $connectable = "<b>Read more</b>";
        }
    }

    print("&nbsp;(&nbsp;<i class='fa fa-arrow-circle-up' style='color:lightgreen' style='font-size:18px' alt='Uploading' title='You are uploading'></i>&nbsp; <a class='nav-top' href=\"javascript:popout(0)\"onclick=\"window.open('".URLROOT."/peers/popoutseed?id=" . $_SESSION["id"] . "','Seeding','width=350,height=350,scrollbars=yes')\" title='You are Seeding'><b>" . $activeseed . "</b></a>&nbsp;");
    print("&nbsp;<i class='fa fa-arrow-circle-down' style='color:blue' style='font-size:18px' alt='Downloading' title='You are downloading'></i>&nbsp;<a class='nav-top' href=\"javascript:popout(0)\"onclick=\"window.open('".URLROOT."/peers/popoutleech?id=" . $_SESSION["id"] . "','Seeding','width=350,height=350,scrollbars=yes')\" title='You are Leeching'>&nbsp;<b>" . $activeleech . "</b>&nbsp;</a>&nbsp;");
    print("<i class='fa fa-refresh fa-spin fa-3x fa-fw' style='font-size:18px' alt='Connected' title='Indicates if you are connectable'></i>&nbsp; " . $connectable . ")&nbsp;");
//////connectable yes or know end of mod////////
        if ($_SESSION["control_panel"] == "yes"): ?>
		<?php
      $arr = DB::run("SELECT * FROM messages WHERE receiver=" . $_SESSION["id"] . " and unread='yes' AND location IN ('in','both')")->fetchAll();
      $unreadmail = count($arr);
    if ($unreadmail !== 0) {
      print("<a href='" . URLROOT . "/messages?type=inbox'><b><font color='red'>$unreadmail</font>&nbsp;<i class='fa fa-envelope' style='font-size:18px' alt='New Message' title='You have a New Message!'></i></b></a>");
    } else {
      print("<a href='" . URLROOT . "/messages/overview'><i class='fa fa-envelope' style='color:navy' style='font-size:18px' alt='Messages' title='Your Messages!'></i></a>");
    }
    ?>
	
       &nbsp;<a href="<?php echo URLROOT; ?>/admincp"><b><font color='grey'><?php echo Lang::T("STAFFCP") ?></font></b></a>&nbsp;
    <?php endif;?>
	   &nbsp;<a href="<?php echo URLROOT; ?>/logout"><i class='fa fa-sign-out'  style='color:red' style='font-size:18px' alt='Logout' title='Logout!'></i></a>
    <?php endif;?>
    </ul>
  </div>
</nav>
