<?php
class Home
{

    public function __construct()
    {
        $this->session = Auth::user(0, 1, true);
    }

    public function index()
    {
        Style::header(Lang::T("HOME"));
        // Check
        if (file_exists("check.php") && $_SESSION["class"] == 7) {
            Style::begin("<font class='error'>" . htmlspecialchars('WARNING') . "</font>");
            echo '<div class="alert ttalert">check still exists, please delete or rename the file as it could pose a security risk<br /><br /><a href="check.php">View /check</a> - Use to check your config!<br /></div>';
            Style::end();
        }
        // Start Hit And Run Warning
        if (HNR_ON) {
            $query = DB::run("SELECT count(hnr) FROM `snatched` WHERE `uid` = '" . $_SESSION["id"] . "' AND `hnr` = 'yes'");
            $res2 = $query->fetch(PDO::FETCH_ASSOC);
            $hnr = "<b><b>&nbsp; " . $res2[0] . " &nbsp;</b>";
            $hnr2 = $res2[0];
            if ($res2[0] > 0) {
                $data = [
                    'hnr1' => $hnr,
                    'hnr2' => $hnr2,
                ];
                View::render('home/hitnrun', $data);
            }
        }
        // Site Notice
        if (SITENOTICEON) {
            $data = [];
            View::render('home/notice', $data);
        }
        // Site News
        if (NEWSON && $_SESSION['view_news'] == "yes") {
            Style::begin(Lang::T("NEWS"));
            $res = DB::run("SELECT news.id, news.title, news.added, news.body, users.username FROM news LEFT JOIN users ON news.userid = users.id ORDER BY added DESC LIMIT 10");
            if ($res->rowCount() > 0) {
                print("<div class='container'><table class='table table-striped'><tr><td>\n<ul>");
                $news_flag = 0;
                while ($array = $res->fetch(PDO::FETCH_LAZY)) {
                    if (!$array["username"]) {
                        $array["username"] = Lang::T('UNKNOWN_USER');
                    }
                    $numcomm = get_row_count("comments", "WHERE news='" . $array['id'] . "'");
                    // Show first 2 items expanded
                    if ($news_flag < 2) {
                        $disp = "block";
                        $pic = "minus";
                    } else {
                        $disp = "none";
                        $pic = "plus";
                    }
                    print("<br /><a href=\"javascript: klappe_news('a" . $array['id'] . "')\"><img border=\"0\" src=\"" . URLROOT . "/assets/images/$pic.gif\" id=\"pica" . $array['id'] . "\" alt=\"Show/Hide\" />");
                    print("&nbsp;<b>" . $array['title'] . "</b></a> - <b>" . Lang::T("POSTED") . ":</b> " . date("d-M-y", TimeDate::utc_to_tz_time($array['added'])) . " <b>" . Lang::T("BY") . ":</b><a href='" . URLROOT . "/profile?id=$array[id]'>  " . Users::coloredname($array['username']) . "</a>");
                    print("<div id=\"ka" . $array['id'] . "\" style=\"display: $disp;\"> " . format_comment($array["body"]) . " <br /><br />" . Lang::T("COMMENTS") . " (<a href='" . URLROOT . "/comments?type=news&amp;id=" . $array['id'] . "'>" . number_format($numcomm) . "</a>)</div>");

                    $news_flag++;
                }
                print("</ul></td></tr></table></div>\n");
            } else {
                echo "<br /><b>" . Lang::T("NO_NEWS") . "</b>";
            }
            Style::end();
        }

        // Shoutbox
        if (SHOUTBOX && !($_SESSION['hideshoutbox'] == 'yes')) {
            $data = [];
            View::render('home/shoutbox', $data);
        }
        // Last Forum Post On Index
        if (FORUMONINDEX) {
            $data = [];
            View::render('home/lastforumpost', $data);
        }

        // Latest Torrents
        if (!$_SESSION['loggedin']) {
            $msg = Lang::T("BROWSE_MEMBERS_ONLY");
            $data = [
                'message' => $msg,
            ];
            View::render('home/ok', $data);
        } else {
            $query = "SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.sticky,  torrents.vip,  torrents.tube,  torrents.imdb, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech,
            categories.name AS cat_name, categories.image AS cat_pic, categories.parent_cat AS cat_parent,
            users.username, users.privacy,
            IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating
            FROM torrents
            LEFT JOIN categories ON category = categories.id
            LEFT JOIN users ON torrents.owner = users.id
            WHERE visible = 'yes' AND banned = 'no'
            ORDER BY sticky ASC, id DESC LIMIT 25";
            $res = DB::run($query); // should use foreach, but the torrenttablefunction is useful
            if ($res->rowCount() > 0) {
                $data = [
                    'torrtable' => $res,
                ];
                View::render('home/torrent', $data);
            } else {
                $data = [];
                View::render('home/nothingfound', $data);
            }
            if ($_SESSION['loggedin'] == true) {
                DB::run("UPDATE users SET last_browse=" . TimeDate::gmtime() . " WHERE id=?", [$_SESSION['id']]);
            }
        }
        // Disclaimer
        if (DISCLAIMERON) {
            $data = [];
            View::render('home/disclaimer', $data);
        }
        Style::footer();
    }

}