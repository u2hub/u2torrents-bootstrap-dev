<?php
class Admingroups extends Controller
{

    public function __construct()
    {
        Auth::user();
        Auth::isStaff();
        // $this->userModel = $this->model('User');
        $this->logsModel = $this->model('Logs');
        $this->valid = new Validation();
    }

    public function index()
    {
        $getlevel = DB::run("SELECT * from `groups` ORDER BY group_id");
        $data = [
            'title' => Lang::T("Groups Management"),
            'getlevel' => $getlevel,
        ];
        $this->view('groups/admin/view', $data, 'admin');
    }

    public function edit()
    {
        $group_id = intval($_GET["group_id"]);
        $rlevel = DB::run("SELECT * FROM `groups` WHERE group_id=?", [$group_id]);
        if (!$rlevel) {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_NO_GROUP_ID_FOUND"), 1);
        }
        $data = [
            'title' => Lang::T("Groups Management"),
            'rlevel' => $rlevel,
        ];
        $this->view('groups/admin/edit', $data, 'admin');
    }

    public function update()
    {
        $group_id = intval($_GET["group_id"]);
        DB::run("UPDATE `groups` SET 
                level = ?, Color = ?, view_torrents = ?, edit_torrents  = ?, delete_torrents = ?,
                view_users = ?, edit_users = ?, delete_users = ?, view_news = ?, edit_news = ?,
                delete_news = ?, view_forum = ?, edit_forum = ?, delete_forum = ?, can_upload = ?,
                can_download = ?, maxslots= ?, control_panel = ?, staff_page = ?, staff_public = ?, staff_sort = ?
                WHERE group_id=?", [
                $_POST["gname"], $_POST["gcolor"], $_POST["vtorrent"], $_POST["etorrent"], $_POST["dtorrent"], 
                $_POST["vuser"], $_POST["euser"], $_POST["duser"], $_POST["vnews"], $_POST["enews"], 
                $_POST["dnews"], $_POST["vforum"], $_POST["eforum"], $_POST["dforum"], $_POST["upload"], 
                $_POST["down"], $_POST["downslots"], $_POST["admincp"],  $_POST["staffpage"], 
                $_POST["staffpublic"], intval($_POST['sort']),  $group_id]);
        Redirect::autolink(URLROOT . "/admingroups/groups", Lang::T("SUCCESS"), "Groups Updated!");
    }

    public function delete()
    {
        //Needs to be secured!!!!
        $group_id = intval($_GET["group_id"]);
        if (($group_id == "1") || ($group_id == "7")) {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_YOU_CANT_DEL_THIS_GRP"), 1);
        }
        DB::run("DELETE FROM `groups` WHERE group_id=?", [$group_id]);
        Redirect::autolink(URLROOT . "/admingroups/groups", Lang::T("CP_DEL_OK"));
    }

    public function add()
    {
        $rlevel = DB::run("SELECT DISTINCT group_id, level FROM `groups` ORDER BY group_id");
        $data = [
            'title' => Lang::T("Groups Management"),
            'rlevel' => $rlevel,
        ];
        $this->view('groups/admin/add', $data, 'admin');
    }

    public function addnew()
    {
        $gname = $_POST["gname"];
        $gcolor = $_POST["gcolor"];
        $group_id = $_POST["getlevel"];
        $rlevel = DB::run("SELECT * FROM `groups` WHERE group_id=?", [$group_id]);
        $level = $rlevel->fetch(PDO::FETCH_ASSOC);
        if (!$level) {
            show_error_msg(Lang::T("ERROR"), Lang::T("CP_INVALID_ID"), 1);
        }
        DB::run("INSERT INTO `groups`
        (level, color, view_torrents, edit_torrents, delete_torrents, view_users, edit_users, delete_users,
	    view_news, edit_news, delete_news, view_forum, edit_forum, delete_forum, can_upload, can_download,
	    control_panel, staff_page, staff_public, staff_sort, maxslots)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
        [$gname, $gcolor, $level['view_torrents'], $level["edit_torrents"], $level["delete_torrents"], $level["view_users"],
        $level["edit_users"], $level["delete_users"], $level["view_news"], $level["edit_news"], $level["delete_news"],
        $level["edit_forum"], $level["edit_forum"], $level["delete_forum"], $level["can_upload"], $level["can_download"], $level["control_panel"],
        $level["staff_page"], $level["staff_public"], $level["staff_sort"], $level["maxslots"]]);
        Redirect::autolink(URLROOT . "/admingroups/groups", Lang::T("SUCCESS"), "Groups Updated!");
    }
}