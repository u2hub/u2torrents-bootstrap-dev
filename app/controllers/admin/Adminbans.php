<?php
class Adminbans
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        Redirect::to(URLROOT . "/admincp");
    }

    public function ip()
    {
        $do = $_GET['do'];
        if ($do == "del") {
            if (!@count($_POST["delids"])) {
                Redirect::autolink(URLROOT . '/adminbans/ip', Lang::T("NONE_SELECTED"));
            }
            $delids = array_map('intval', $_POST["delids"]);
            $delids = implode(', ', $delids);
            $res = DB::run("SELECT * FROM bans WHERE id IN ($delids)");
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                DB::run("DELETE FROM bans WHERE id=$row[id]");
                Logs::write("IP Ban ($row[first] - $row[last]) was removed by $_SESSION[id] ($_SESSION[username])");
            }
            Redirect::autolink(URLROOT . '/adminbans/ip', "Ban(s) deleted.");
        }

        if ($do == "add") {
            $first = trim($_POST["first"]);
            $last = trim($_POST["last"]);
            $comment = trim($_POST["comment"]);
            if ($first == "" || $last == "" || $comment == "") {
                Redirect::autolink(URLROOT . '/adminbans/ip', Lang::T("MISSING_FORM_DATA"));
            }
            $comment = $comment;
            $added = TimeDate::get_date_time();
            $bins = DB::run("INSERT INTO bans (added, addedby, first, last, comment) VALUES(?,?,?,?,?)", [$added, $_SESSION['id'], $first, $last, $comment]);
            $err = $bins->errorCode();
            switch ($err) {
                case 1062:
                    Redirect::autolink(URLROOT . '/adminbans/ip', "Duplicate ban.");
                    break;
                case 0:
                    Redirect::autolink(URLROOT . '/adminbans/ip', "Ban added.");
                    break;
                default:
                    Redirect::autolink(URLROOT . '/adminbans/ip', Lang::T("THEME_DATEBASE_ERROR") . " " . htmlspecialchars($bins->errorInfo()));
            }
        }

        $count = get_row_count("bans");
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, URLROOT . "/adminbans/ip?"); // 50 per page
        $res = DB::run("SELECT bans.*, users.username FROM bans LEFT JOIN users ON bans.addedby=users.id ORDER BY added $limit");

        $data = [
            'title' => Lang::T("BANNED_IPS"),
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'pagertop' => $pagertop,
            'res' => $res,
        ];
        View::render('bans/admin/ip', $data, 'admin');
    }

    public function email()
    {
        $remove = (int) $_GET['remove'];
        if (Validate::Id($remove)) {
            DB::run("DELETE FROM email_bans WHERE id=$remove");
            Logs::write(sprintf(Lang::T("EMAIL_BANS_REM"), $remove, $_SESSION["username"]));
            Redirect::autolink(URLROOT . '/adminbans/email', Lang::T("EMAIL_BAN_DELETED"));
        }
        if ($_GET["add"] == '1') {
            $mail_domain = trim($_POST["mail_domain"]);
            $comment = trim($_POST["comment"]);
            if (!$mail_domain || !$comment) {
                Redirect::autolink(URLROOT . '/adminbans/email', Lang::T("MISSING_FORM_DATA") . ".");
                die;
            }
            $mail_domain = $mail_domain;
            $comment = $comment;
            $added = TimeDate::get_date_time();
            DB::run("INSERT INTO email_bans (added, addedby, mail_domain, comment) VALUES(?,?,?,?)", [$added, $_SESSION['id'], $mail_domain, $comment]);
            Logs::write(sprintf(Lang::T("EMAIL_BANS_ADD"), $mail_domain, $_SESSION["username"]));
            Redirect::autolink(URLROOT . '/adminbans/email', Lang::T("EMAIL_BAN_ADDED"));
        }

        $count = DB::run("SELECT count(id) FROM email_bans")->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(40, $count, URLROOT . "/admin/emailbans?");
        $res = DB::run("SELECT * FROM email_bans ORDER BY added DESC $limit");

        $title = Lang::T("EMAIL_BANS");
        $data = [
            'title' => $title,
            'count' => $count,
            'pagertop' => $pagertop,
            'res' => $res,
            'pagerbottom' => $pagerbottom,
            'limit' => $limit,
        ];
        View::render('bans/admin/email', $data, 'admin');
    }

    public function torrent()
    {
        $count = DB::run("SELECT COUNT(*) FROM torrents WHERE banned=?", ['yes'])->fetchColumn();
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, URLROOT."/adminbans/torrent?");
        $resqq = DB::run("SELECT id, name, seeders, leechers, visible, banned, external FROM torrents WHERE banned=? ORDER BY name", ['yes']);
        
        $title = "Banned " . Lang::T("TORRENT_MANAGEMENT");
        $data = [
            'title' => $title,
            'pagerbottom' => $pagerbottom,
            'count' => $count,
            'pagertop' => $pagertop,
            'resqq' => $resqq,
        ];
        View::render('bans/admin/torrents', $data, 'admin');
    }

}