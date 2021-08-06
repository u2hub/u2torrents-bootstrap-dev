<?php
class Snatched
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        $tid = (int) $_GET['tid'];
        $uid = (int) $_GET['uid'];
        $id = (int) $_GET['id'];

        if ($id != 0) {
            $uid = (int) $_GET['id'];
        }
        if ($tid == 0 && $uid == 0) {
            $uid = (int) $_SESSION['id'];
        }
        if ($tid > 0) {
            $count_tid = get_row_count('snatched', 'WHERE `tid` = \'' . $tid . '\'');
            $type = "torrent";
            $count_uid = 0;
        } else {
            $count_uid = get_row_count('snatched', 'WHERE `uid` = \'' . $uid . '\'');
            $type = "user";
            $count_tid = 0;
        }

        if ($type == 'torrent') {
            //===| Start Torrents Snatched
            if ($count_tid > 0) {
                $torrents = DB::run("SELECT `name` FROM `torrents` WHERE `id` = '$tid'")->fetchColumn();
                //    $torrents = mysqli_fetch_row($torrent);
                $title = "" . Lang::T("REGISTERED_MEMBERS_TO_TORRENT") . " " . htmlspecialchars($torrents) . "";
                Style::header($title);
                Style::begin($title);
                $perpage = 50;
                list($pagertop, $pagerbottom, $limit) = pager($perpage, $count_tid, '/snatched?tid=' . $tid . ' &amp;');
                $qry = "SELECT
				users.id,
				users.username,
				users.class,
				snatched.uid as uid,
				snatched.tid as tid,
				snatched.uload,
				snatched.dload,
				snatched.stime,
				snatched.utime,
				snatched.ltime,
				snatched.completed,
				snatched.hnr,
				(
					SELECT seeder
					FROM peers
					WHERE torrent = tid AND userid = uid LIMIT 1
				) AS seeding
				FROM
				snatched
				INNER JOIN users ON snatched.uid = users.id
				INNER JOIN torrents ON snatched.tid = torrents.id
				WHERE
				users.status = 'confirmed' AND
				torrents.banned = 'no' AND snatched.tid = '$tid'
				ORDER BY stime DESC $limit";

                $res = DB::run($qry);
                if ($count_tid > $perpage) {echo ($pagertop);}
                if ($res->rowCount() > 0):
                ?>
			    <table border="0" class="table_table" cellpadding="4" cellspacing="0" width="100%">
				<tr>
					<th class="table_head" align="left"><?php echo Lang::T("USERNAME"); ?></th>
					<th class="table_head"><?php echo Lang::T("UPLOADED"); ?></th>
					<th class="table_head"><?php echo Lang::T("DOWNLOADED"); ?></th>
					<th class="table_head"><?php echo Lang::T("RATIO"); ?></th>
					<th class="table_head"><?php echo Lang::T("ADDED"); ?></th>
					<th class="table_head"><?php echo Lang::T("LAST_ACTION"); ?></th>
					<th class="table_head"><img src="assets/images/seedtime.png" border="0" title="<?php echo Lang::T("SEED_TIME"); ?>"></th>
					<th class="table_head"><img src="assets/images/check.png" border="0" title="<?php echo Lang::T("COMPLETED"); ?>"></th>
					<th class="table_head"><img src="assets/images/seed.gif" border="0" title="<?php echo Lang::T("SEEDING"); ?>"></th>
					<th class="table_head"><?php echo Lang::T("HNR"); ?></th>
				</tr>

				<?php
            while ($row = $res->fetch(PDO::FETCH_LAZY)):

                    if ($row[6] > 0) {$ratio = number_format($row[5] / $row[6], 2);} else { $ratio = "---";}
                    $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";

                    $startdate = TimeDate::utc_to_tz(TimeDate::get_date_time($row[7]));
                    $lastaction = TimeDate::utc_to_tz(TimeDate::get_date_time($row[8]));

                    if ($row[11] != "yes") {$hnr = "<font color=#27B500><b>" . Lang::T("NO") . "</b></font>";} else { $hnr = "<font color=#FF1200><b>" . Lang::T("YES") . "</b></font>";}
                    if ($row[12] != "yes") {$seed = "<font color=#FF1200><b>" . Lang::T("NO") . "</b></font>";} else { $seed = "<font color=#27B500><b>" . Lang::T("YES") . "</b></font>";}
                    ?>

					<tr align="center">
						<td class="table_col1" align="left"><a href="<?PHP echo URLROOT ?>/profile?id=<?php echo $row[0]; ?>"><?php echo "<b>" . $row[1] . "</b>"; ?></a></td>
						<td class="table_col2"><font color="#27B500"><?php echo mksize($row[5]); ?></font></td>
						<td class="table_col1"><font color="#FF1200"><?php echo mksize($row[6]); ?></font></td>
						<td class="table_col2"><?php echo $ratio; ?></td>
						<td class="table_col2"><?php echo date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($startdate)); ?></td>
						<td class="table_col1"><?php echo date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($lastaction)); ?></td>
						<td class="table_col1"><?php echo ($row[9]) ? TimeDate::mkprettytime($row[9]) : '---'; ?></td>
						<td class="table_col2"><?php echo ($row[10]) ? "<font color=#0080FF><b>" . Lang::T("YES") . "</b></font>" : "<b>" . Lang::T("NO") . "</b>"; ?></td>
						<td class="table_col1"><?php echo $seed; ?></td>
						<td class="table_col2"><?php echo $hnr; ?></td>
					</tr>
					<?php
            endwhile;
                ?>
			</table>
			<?php
            if ($count_tid > $perpage) {echo ($pagerbottom);}
                print("<div style='margin-top:5px; margin-bottom:10px' align='right'><a href=".URLROOT."/torrent?id=$tid><b><input type='submit' value='" . Lang::T("BACK_TO_TORRENT") . "'></b></a></div>");
                endif;
                Style::end();
                Style::footer();
                die;
            } else {
                if ($tid >= 1) {
                    $torrents = DB::run("SELECT `name` FROM `torrents` WHERE `id` = '$tid'")->fetchColumn();
                    //    $torrents = mysqli_fetch_row($torrent);
                    $ttitle = "" . Lang::T("SNATCHLIST_FOR") . " " . htmlspecialchars($torrents) . "";
                    if ($torrents[0] == '') {$ttitle = "" . Lang::T("NO_TORRENT_WITH_ID") . " $tid";}

                    Style::header($ttitle);
                    Style::begin($ttitle);
                    ?>
			    	<div style="margin-top:10px; margin-bottom:10px" align="center"><font size="2"><?php echo Lang::T("NOTHING_FOUND"); ?>.</font></div>
			    	<div style="margin-bottom:10px" align="center">[<?php echo "<a href=".URLROOT."/torrent?id=$tid>"; ?><b><?php echo Lang::T("BACK_TO_TORRENT"); ?></b></a>]</div>
				    <?php
                    Style::end();
                    Style::footer();
                    die;
                }
            }
            //===| End Torrents Snatched
            } else {
            //===| Start Users Snatched
            if ($count_uid > 0) {
                $users = DB::run("SELECT `username` FROM `users` WHERE `id` = '$uid'")->fetchColumn();
                $title = "" . Lang::T("SNATCHLIST_FOR") . " " . htmlspecialchars($users) . "";
                $title2 = "" . Lang::T("SNATCHLIST_FOR") . " " . $users . "";
                Style::header($title);
                Style::begin($title2);

                if (($_SESSION["control_panel"] == "no") && $_SESSION["id"] != $uid) {
                    echo "<div style='margin-top:5px; margin-bottom:5px' align='center'><font size='4' color='red'><b>" . Lang::T("NO_PERMISSION") . "</b></font></div>";
                    Style::end();
                    Style::footer();
                    die;
                }

                $perpage = 50;
                list($pagertop, $pagerbottom, $limit) = pager($perpage, $count_uid, '/snatched?uid=' . $uid . ' &amp;');

                $qry = "SELECT
				snatched.tid as tid,
				torrents.name,
				snatched.uload,
				snatched.dload,
				snatched.stime,
				snatched.utime,
				snatched.ltime,
				snatched.completed,
				snatched.hnr,
				(
					SELECT seeder
					FROM peers
					WHERE torrent = tid AND userid = $uid LIMIT 1
				) AS seeding
				FROM
				snatched
				INNER JOIN users ON snatched.uid = users.id
				INNER JOIN torrents ON snatched.tid = torrents.id
				WHERE
				users.status = 'confirmed' AND
				torrents.banned = 'no' AND snatched.uid = '$uid'
				ORDER BY stime DESC $limit";

                $res = DB::run($qry);

                if ($uid == $_SESSION['id']) {
                    usermenu($id);

                    print("<div style='margin-top:20px; margin-bottom:20px' align='center'><font size='2'>" . Lang::T("SNATCHED_MESSAGE") . "</font></div>");
                }

                if ($uid != $_SESSION['id']) {
                    print("<div style='margin-top:10px; margin-bottom:5px'><a href=".URLROOT."/profile?id=$uid><b><input type='submit' value='" . Lang::T("GO_TO_USER_ACCOUNT") . "'></b></a></div>");
                }

                if ($count_uid > $perpage) {echo $pagertop;}

                if ($res->rowCount() > 0):
                ?>
		    	<table border="0" class="table_table" cellpadding="4" cellspacing="0" width="100%">
					<tr>
						<th class="table_head" align="left"><?php echo Lang::T("TORRENT_NAME"); ?></th>
					  <?php if (ALLOWEXTERNAL) {?>
						<th class="table_head"><img src="assets/images/t_le.png" border="0" title="<?php echo Lang::T("T_L_OR_E"); ?>"></th>
					  <?php }?>
						<th class="table_head"><?php echo Lang::T("UPLOADED"); ?></th>
						<th class="table_head"><?php echo Lang::T("DOWNLOADED"); ?></th>
						<th class="table_head"><?php echo Lang::T("RATIO"); ?></th>
						<th class="table_head"><?php echo Lang::T("ADDED"); ?></th>
						<th class="table_head"><?php echo Lang::T("LAST_ACTION"); ?></th>
						<th class="table_head"><img src="assets/images/seedtime.png" border="0" title="<?php echo Lang::T("SEED_TIME"); ?>"></th>
						<th class="table_head"><img src="assets/images/check.png" border="0" title="<?php echo Lang::T("COMPLETED"); ?>"></th>
						<th class="table_head"><img src="assets/images/seed.gif" border="0" title="<?php echo Lang::T("SEEDING"); ?>"></th>
						<th class="table_head"><?php echo Lang::T("HNR"); ?></th>
					</tr>

					<?php
                    while ($row = $res->fetch(PDO::FETCH_LAZY)):

                    $startdate = TimeDate::utc_to_tz(TimeDate::get_date_time($row[4]));
                    $lastaction = TimeDate::utc_to_tz(TimeDate::get_date_time($row[5]));

                    $query = DB::run("SELECT external, freeleech FROM torrents WHERE id = $row[0]");
                    $result = $query->fetch();
                    if ($result[0] == "yes") {$type = "<img src='assets/images/t_extern.png' border='0' title='" . Lang::T("EXTERNAL_TORRENT") . "'>";} else { $type = "<img src='assets/images/t_local.png' border='0' title='" . Lang::T("LOCAL_TORRENT") . "'>";}
                    if ($result[1] == 1) {$freeleech = "<img src='assets/images/free.gif' border='0' title='" . Lang::T("FREE") . "'>";} else { $freeleech = "";}

                    if ($row[3] > 0) {$ratio = number_format($row[2] / $row[3], 2);} else { $ratio = "---";}
                    $ratio = "<font color=" . get_ratio_color($ratio) . ">$ratio</font>";

                    if ($row[8] != "yes") {$hnr = "<font color=#27B500><b>" . Lang::T("NO") . "</b></font>";} else { $hnr = "<font color=#FF1200><b>" . Lang::T("YES") . "</b></font>";}
                    if ($row[9] != "yes") {$seed = "<font color=#FF1200><b>" . Lang::T("NO") . "</b></font>";} else { $seed = "<font color=#27B500><b>" . Lang::T("YES") . "</b></font>";}

                    $maxchar = 30; //===| cut name length
                    $smallname = htmlspecialchars(CutName($row[1], $maxchar));
                    ?>
						<tr align="center">  <!-- below was ".(count($expandrows)?" -->
							<?php echo ("<td class='ttable_col1' align='left' nowrap='nowrap'>" . ($expandrows ? "<a href=\"javascript: klappe_torrent('t" . $row['0'] . "')\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/plus.gif\" id=\"pict" . $row['0'] . "\" alt=\"Show/Hide\" class=\"showthecross\" /></a>" : "") . "<a title=\"" . $row["1"] . "\" href=\"/torrent?id=" . $row['0'] . "&amp;hit=1\"><b>$smallname</b></a> $freeleech</td>"); ?>
						  <?php if (ALLOWEXTERNAL) {?>
							<td class="table_col2" align="center"><?php echo $type; ?></td>
						  <?php }?>
							<td class="table_col1"><font color="#27B500"><?php echo mksize($row[2]); ?></font></td>
							<td class="table_col2"><font color="#FF1200"><?php echo mksize($row[3]); ?></font></td>
							<td class="table_col1"><?php echo $ratio; ?></td>
							<td class="table_col2"><?php echo date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($startdate)); ?></td>
							<td class="table_col1"><?php echo date('d.M.Y H:i', TimeDate::sql_timestamp_to_unix_timestamp($lastaction)); ?></td>
							<td class="table_col2"><?php echo ($row[6]) ? TimeDate::mkprettytime($row[6]) : '---'; ?></td>
							<td class="table_col1"><?php echo ($row[7]) ? "<font color=#0080FF><b>" . Lang::T("YES") . "</b></font>" : "<b>" . Lang::T("NO") . "</b>"; ?></td>
							<td class="table_col2"><?php echo $seed; ?></td>
							<td class="table_col1"><?php echo $hnr; ?></td>
						</tr>
						<?php
                endwhile;
                ?>
				</table>
			    <?php

                if ($count_uid > $perpage) {echo $pagerbottom;}

                if ($uid != $_SESSION['id']) {
                    print("<div style='margin-top:5px; margin-bottom:10px' align='right'><a href=".URLROOT."/profile?id=$uid><b><input type='submit' value='" . Lang::T("GO_TO_USER_ACCOUNT") . "'></b></a></div>");
                }

                endif;
                Style::end();
                Style::footer();
                die;
            } else {
                $users = DB::run("SELECT `username` FROM `users` WHERE `id` = '$uid'")->fetchColumn();

                $title = "" . Lang::T("SNATCHLIST_FOR") . " " . htmlspecialchars($users) . "";
                if ($users[0] == '') {$title = "" . Lang::T("NO_USER_WITH_ID") . " $uid";}

                $title2 = "" . Lang::T("SNATCHLIST_FOR") . " " . $users . "";
                if ($users[0] == '') {$title2 = "" . Lang::T("NO_USER_WITH_ID") . " $uid";}

                Style::header($title);
                Style::begin($title2);

                if (($_SESSION["control_panel"] == "no") && $_SESSION["id"] != $uid) {
                    echo "<div style='margin-top:10px; margin-bottom:10px' align='center'><font size='2' color='red'><b>" . Lang::T("NO_PERMISSION") . "</b></font></div>";
                    Style::end();;
                    Style::footer();
                    die;
                }
                echo "<div style='margin-top:10px; margin-bottom:10px' align='center'><font size='2'>" . Lang::T("NOTHING_FOUND") . ".</font></div>";

                if (!$users)
                //    if ($users->rowCount() > 0)
                {
                    if ($uid != $_SESSION['id']) {
                        print("<div style='margin-bottom:10px' align='center'><a href=".URLROOT."/profile?id=$uid><b><button type='submit' class='btn btn-sm ttbtn'>" . Lang::T("GO_TO_USER_ACCOUNT") . "</button></b></a></div>");
                    } else {
                        print("<div style='margin-bottom:10px' align='center'>[<a href=".URLROOT."/profile?id=$_SESSION[id]><b>" . Lang::T("GO_TO_YOUR_PROFILE") . "</b></a>]</div>");
                    }
                }
                Style::end();
                Style::footer();
            }
        }
    }
}