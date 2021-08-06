<?php
class Adminsearch
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function simplesearch()
    {
        if ($_SESSION['delete_users'] == 'no' || $_SESSION['delete_torrents'] == 'no') {
            Redirect::autolink(URLROOT . "/admincp", "You do not have permission to be here.");
        }
        if ($_POST['do'] == "del") {
            if (!@count($_POST["users"])) {
                Redirect::autolink(URLROOT."/adminsearch/simplesearch", "Nothing Selected.");
            }
            $ids = array_map("intval", $_POST["users"]);
            $ids = implode(", ", $ids);
            $res = DB::run("SELECT `id`, `username` FROM `users` WHERE `id` IN ($ids)");
            while ($row = $res->fetch(PDO::FETCH_LAZY)) {
                Logs::write("Account '$row[1]' (ID: $row[0]) was deleted by $_SESSION[username]");
                Users::deleteuser($row[0]);
            }
            if ($_POST['inc']) {
                $res = DB::run("SELECT `id`, `name` FROM `torrents` WHERE `owner` IN ($ids)");
                while ($row = $res->fetch(PDO::FETCH_LAZY)) {
                    Logs::write("Torrent '$row[1]' (ID: $row[0]) was deleted by $_SESSION[username]");
                    deletetorrent($row["id"]);
                }
            }
            Redirect::autolink(URLROOT . "/adminsearch/simplesearch", "Entries Deleted");
        }
        $where = null;
        if (!empty($_GET['search'])) {
            $search = sqlesc('%' . $_GET['search'] . '%');
            $where = "AND username LIKE " . $search . " OR email LIKE " . $search . "
                 OR ip LIKE " . $search;
        }
        $count = get_row_count("users", "WHERE enabled = 'yes' AND status = 'confirmed' $where");
        list($pagertop, $pagerbottom, $limit) = pager(25, $count, '/adminsearch/simpleusersearch?;');
        $res = DB::run("SELECT id, username, class, email, ip, added, last_access FROM users WHERE enabled = 'yes' AND status = 'confirmed' $where ORDER BY username DESC $limit");

        $data = [
            'title' => Lang::T("USERS_SEARCH_SIMPLE"),
            'count' => $count,
            'pagerbottom' => $pagerbottom,
            'res' => $res,
        ];
        View::render('user/admin/simpleusersearch', $data, 'admin');
    }

    public function advancedsearch()
    {
        $do = $_GET['do']; // todo
        if ($do == "warndisable") {
            if (empty($_POST["warndisable"])) {
                Redirect::autolink(URLROOT."/adminsearch/advancedsearh", "You must select a user to edit.", 1);
            }
            if (!empty($_POST["warndisable"])) {
                $enable = $_POST["enable"];
                $disable = $_POST["disable"];
                $unwarn = $_POST["unwarn"];
                $warn = $_POST["warn"];
                $warnlength = (int) $_POST["warnlength"];
                $warnpm = $_POST["warnpm"];
                $_POST['warndisable'] = array_map("intval", $_POST['warndisable']);
                $userid = implode(", ", $_POST['warndisable']);
                if ($disable != '') {
                    DB::run("UPDATE users SET enabled='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
                }
                if ($enable != '') {
                    DB::run("UPDATE users SET enabled='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
                }
                if ($unwarn != '') {
                    $msg = "Your Warning Has Been Removed";
                    foreach ($_POST["warndisable"] as $userid) {
                        $qry = DB::run("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES (?,?,?,?,?)", [0, 0, $userid, TimeDate::get_date_time(), $msg]);
                        if (!$qry) {
                            die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $qry->errorCode() . ") " . $qry->errorInfo());
                        }
                    }
                    $r = DB::run("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $r->errorCode() . ") " . $r->errorInfo());
                    $user = $r->fetch(PDO::FETCH_LAZY);
                    $exmodcomment = $user["modcomment"];
                    $modcomment = gmdate("Y-m-d") . " - Warning Removed By " . $_SESSION['username'] . ".\n" . $modcomment . $exmodcomment;
                    $query = "UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
                    $q = DB::run($query);
                    if (!$q) {
                        die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $q->errorCode() . ") " . $q->errorInfo());
                    }

                    DB::run("UPDATE users SET warned='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
                }
                if ($warn != '') {
                    if (empty($_POST["warnpm"])) {
                        Redirect::autolink(URLROOT."/adminsearch/advancedsearh", "You must type a reason/mod comment.", 1);
                    }

                    $msg = "You have received a warning, Reason: $warnpm";
                    $user = DB::run("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")->fetch();
                    $exmodcomment = $user["modcomment"];
                    $modcomment = gmdate("Y-m-d") . " - Warned by " . $_SESSION['username'] . ".\nReason: $warnpm\n" . $modcomment . $exmodcomment;
                    $query = "UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")";
                    $upd = DB::run($query);
                    if (!$upd) {
                        die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n" . Lang::T("ERROR") . ": (" . $upd->errorCode() . ") " . $upd->errorInfo());
                    }

                    DB::run("UPDATE users SET warned='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
                    foreach ($_POST["warndisable"] as $userid) {
                        $ins = DB::run("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES (?,?,?,?,?)", [0, 0, $userid, TimeDate::get_date_time(), $msg]);
                        if (!$ins) {
                            die("<b>A fatal MySQL error occured</b>.\n <br />\n" . Lang::T("ERROR") . ": (" . $ins->errorCode() . ") " . $ins->errorInfo());
                        }

                    }
                }
            }
            Redirect::autolink("$_POST[referer]", "Redirecting back");
            die;
        }

        $title = Lang::T("ADVANCED_USER_SEARCH");
        require APPROOT . '/views/admin/header.php';
        Style::adminnavmenu();
        require APPROOT . '/views/user/admin/advancedsearch.php';
        require APPROOT . '/views/admin/footer.php';
    }

}