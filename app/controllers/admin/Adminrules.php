<?php
class Adminrules extends Controller
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
        $res = DB::run("SELECT * FROM rules ORDER BY id");
        $data = [
            'title' => Lang::T("SITE_RULES_EDITOR"),
            'res' => $res,
        ];
        $this->view('rules/admin/rulesview', $data, 'admin');
    }

    public function edit()
    {
        if ($_GET["save"] == "1") {
            $id = (int) $_POST["id"];
            $title = $_POST["title"];
            $text = $_POST["text"];
            $public = $_POST["public"];
            $class = $_POST["class"];
            DB::run("update rules set title=?, text=?, public=?, class=? where id=?", [$title, $text, $public, $class, $id]);
            Logs::write("Rules have been changed by ($_SESSION[username])");
            show_error_msg(Lang::T("COMPLETE"), "Rules edited ok<br /><br /><a href=" . URLROOT . "/adminrules>Back To Rules</a>", 1);
            die;
        }
        $id = (int) $_POST["id"];
        $res = DB::run("select * from rules where id='$id'");
        $data = [
            'title' => Lang::T("SITE_RULES_EDITOR"),
            'id' => $id,
            'res' => $res,
        ];
        $this->view('rules/admin/rulesedit', $data, 'admin');
    }

    public function addsect()
    {
        if ($_GET["save"] == "1") {
            $title = $_POST["title"];
            $text = $_POST["text"];
            $public = $_POST["public"];
            $class = $_POST["class"];
            DB::run("insert into rules (title, text, public, class) values(?,?,?,?)", [$title, $text, $public, $class]);
            show_error_msg(Lang::T("COMPLETE"), "New Section Added<br /><br /><a href=" . URLROOT . "/adminrules>Back To Rules</a>", 1);
            die();
        }
        $data = [
            'title' => Lang::T("SITE_RULES_EDITOR"),
        ];
        $this->view('rules/admin/rulesaddsect', $data, 'admin');
    }

}