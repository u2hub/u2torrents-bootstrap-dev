<?php
class Peers extends Controller
{
    public function __construct()
    {
        Auth::user();
        // $this->userModel = $this->model('User');
        $this->valid = new Validation();
    }

    public function index()
    {
        Redirect::to(URLROOT);
    }

    // sharing on account details
    public function seeding()
    {
        Style::header("User CP");
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            Session::flash('info', "Bad ID.", URLROOT."/home");
        }
        $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
        if (!$user) {
            Session::flash('info', Lang::T("NO_USER_WITH_ID") . " $id.", URLROOT."/home");
        }
        //add invites check here
        if ($_SESSION["view_users"] == "no" && $_SESSION["id"] != $id) {
            Session::flash('info', Lang::T("NO_USER_VIEW"), URLROOT."/home");
        }
        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $_SESSION["edit_users"] == "no") {
            Session::flash('info', Lang::T("NO_ACCESS_ACCOUNT_DISABLED"), URLROOT."/home");
        }
        $res = DB::run("SELECT torrent, uploaded, downloaded FROM peers WHERE userid =? AND seeder =?", [$id, 'yes']);
        if ($res->rowCount() > 0) {
            $seeding = peerstable($res);
        }

        $res = DB::run("SELECT torrent, uploaded, downloaded FROM peers WHERE userid =? AND seeder =?", [$id, 'no']);
        if ($res->rowCount() > 0) {
            $leeching = peerstable($res);
        }
        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($user["username"]));
        $data = [
            'id' => $id,
            'title' => $title,
            'leeching' => $leeching,
            'seeding' => $seeding,
            'uid' => $user["id"],
            'username' => $user["username"],
            'privacy' => $user["privacy"],
        ];
        $this->view('peers/seeding', $data, 'user');
    }

    // sharing on account details
    public function uploaded()
    {
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            Session::flash('info', "Bad ID.", URLROOT."/home");
        }
        $user = DB::run("SELECT * FROM users WHERE id=?", [$id])->fetch();
        if (!$user) {
            Session::flash('info', Lang::T("NO_USER_WITH_ID") . " $id.", URLROOT."/home");
        }
        //add invites check here
        if ($_SESSION["view_users"] == "no" && $_SESSION["id"] != $id) {
            Session::flash('info', Lang::T("NO_USER_VIEW"), URLROOT."/home");
        }
        if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $_SESSION["edit_users"] == "no") {
            Session::flash('info', Lang::T("NO_ACCESS_ACCOUNT_DISABLED"), URLROOT."/home");
        }
        $page = (int) $_GET["page"];
        $perpage = 25;
        $where = "";
        if ($_SESSION['control_panel'] != "yes") {
            $where = "AND anon='no'";
        }
        $count = DB::run("SELECT COUNT(*) FROM torrents WHERE owner='$id' $where")->fetchColumn();
        unset($where);
        $orderby = "ORDER BY id DESC";
        //get sql info
        if ($count) {
            list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, URLROOT."/profile?id=$id&amp;");
            $res = DB::run("SELECT torrents.id, torrents.category, torrents.leechers, torrents.imdb, torrents.tube, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, torrents.anon, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.announce FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE owner = $id $orderby $limit");
        } else {
            unset($res);
        }
        $title = sprintf(Lang::T("USER_DETAILS_FOR"), Users::coloredname($user["username"]));
        $data = [
            'id' => $id,
            'title' => $title,
            'username' => $user["username"],
            'privacy' => $user["privacy"],
            'count' => $count,
            'res' => $res,
            'pagertop' => $pagertop,
            'pagerbottom' => $pagerbottom,
        ];
        $this->view('peers/uploaded', $data, 'user');
    }

    // sharing on torrent details
    public function peerlist()
    {
        $id = (int) $_GET["id"];
        if (!$this->valid->validId($id)) {
            Session::flash('info', Lang::T("THATS_NOT_A_VALID_ID"), URLROOT."/home");
        }
        if ($_SESSION["view_torrents"] == "no") {
            Session::flash('info', Lang::T("NO_TORRENT_VIEW"), URLROOT."/home");
        }
        //GET ALL MYSQL VALUES FOR THIS TORRENT
        $res = DB::run("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.imdb, torrents.tube, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id");
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $char1 = 50; //cut length
        $shortname = CutName(htmlspecialchars($row["name"]), $char1);
        $title = Lang::T("TORRENT_DETAILS_FOR") . " \"" . $shortname . "\"";
        if ($row["external"] = 'yes') {
            $query = DB::run("SELECT * FROM peers WHERE torrent = $id ORDER BY seeder DESC");
            $result = $query->rowCount();
            if ($result == 0) {
                
                Session::flash('info', Lang::T("NO_ACTIVE_PEERS") . " $id.", URLROOT);
            } else {
                $data = [
                    'id' => $id,
                    'title' => $title,
                    'query' => $query,
                ];
                $this->view('peers/peerlist', $data, 'user');
            }
        }
    }

    // popout seed
    public function popoutseed()
    {$id = (int) $_GET["id"];
        if ($id != $_SESSION["id"]) {
            echo "Not allowed to view others activity here.";
        }
        $res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='yes'");
        if ($res->rowCount() > 0) {
            $seeding = peerstable($res);
        }
        if ($seeding) {
            print("$seeding");
        }
        if (!$seeding) {
            print("<B>Currently not seeding<BR><BR><a href=\"javascript:self.close()\">close window</a><BR>");
        }
    }

    // popout leech
    public function popoutleech()
    {
        $id = (int) $_GET["id"];
        if ($id != $_SESSION["id"]) {
            echo "Not allowed to view others activity here.";
        }
        $res = DB::run("SELECT torrent,uploaded,downloaded FROM peers LEFT JOIN torrents ON torrent = torrents.id WHERE userid='$id' AND seeder='no'");
        if ($res->rowCount() > 0) {
            $leeching = peerstable($res);
        }
        if ($leeching) {
            print("$leeching");
        }
        if (!$leeching) {
            print("<B>Not currently leeching!<BR><br><a href=\"javascript:self.close()\">close window</a><BR>\n");
        }
    }

    // dead torrents
    public function dead()
    {
        if ($_SESSION["control_panel"] != "yes") {
            Session::flash('info', Lang::T("SORRY_NO_RIGHTS_TO_ACCESS"), URLROOT."/home");
        }
        $page = (int) $_GET["page"];
        $perpage = 50;
        $res2 = DB::run("SELECT COUNT(*) FROM torrents WHERE banned = 'no' AND seeders < 1");
        $row2 = $res2->fetch(PDO::FETCH_LAZY);
        $count = $row2[0];
        list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, URLROOT."/peers/dead&amp;");
        $res = DB::run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, torrents.last_action, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.seeders < 1 ORDER BY torrents.added DESC $limit");
    
        if ($_POST["do"] == "delete") {
            if (!@count($_POST["torrentids"])) {
                $msg = "<div style='margin-top:10px; margin-bottom:10px' align='center'>You must select at least one torrent.  &nbsp; [<a href='".URLROOT."/peers/dead'><b>Return </b></a>]</div>";
                Session::flash('info', $msg, URLROOT."/home");
            }
            foreach ($_POST["torrentids"] as $id) {
                deletetorrent(intval($id));
                Logs::write("<a href=".URLROOT."/profile?id=$_SESSION[id]><b>$_SESSION[username]</b></a>deleted the torrent ID : [<b>$id</b>]of the page: <i><b>Dead Torrents </b></i>");
            }
            Session::flash('info', "The selected torrent has been successfully deleted.", URLROOT."/peers/dead");
        }

        if ($count < 1) {
            $msg = "<div style='margin-top:10px; margin-bottom:10px' align='center'><font size='2'><i>...No Dead Torrents !</i></font></div>";
            Session::flash('info', $msg, URLROOT."/home");
        }
        $title = "The Dead Torrents";
        $data = [
            'res' => $res,
            'title' => $title,
             'count' => $count,
             'perpage' => $perpage,
             'pagertop' => $pagertop,
            'pagerbottom' => $pagerbottom,
        ];
        $this->view('peers/dead', $data, 'user');
    }
}