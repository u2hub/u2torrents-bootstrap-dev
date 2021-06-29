<?php

class Import extends Controller
{

    public function __construct()
    {
        Auth::user();
        $this->torrentModel = $this->model('Torrents');
        $this->valid = new Validation();
        $this->logsModel = $this->model('Logs');
    }

    public function index()
    {
        $dir = IMPORT;
        //ini_set("upload_max_filesize",$max_torrent_size);
        $files = array();
        $dh = opendir("$dir/");
        while (false !== ($file = readdir($dh))) {
            if (preg_match("/\.torrent$/i", $file)) {
                $files[] = $file;
            }
        }
        closedir($dh);
        // check access and rights
        if ($_SESSION["edit_torrents"] != "yes") {
            show_error_msg(Lang::T("ERROR"), Lang::T("ACCESS_DENIED"), 1);
        }
        //generate announce_urls[] from config.php
        $announce_urls = explode(",", strtolower(ANNOUNCELIST));

        if ($_POST["takeupload"] == "yes") {
            set_time_limit(0);
            Style::header(Lang::T("UPLOAD_COMPLETE"));
            Style::begin(Lang::T("UPLOAD_COMPLETE"));
            echo "<center>";
            //check form data
            $catid = (int) $_POST["type"];
            if (!$this->valid->validId($catid)) {
                $message = Lang::T("UPLOAD_NO_CAT");
            }

            if (empty($message)) {
                $r = DB::run("SELECT name, parent_cat FROM categories WHERE id=$catid")->fetch();
                echo "<b>Category:</b> " . htmlspecialchars($r[1]) . " -> " . htmlspecialchars($r[0]) . "<br />";
                for ($i = 0; $i < count($files); $i++) {
                    $fname = $files[$i];
                    $descr = Lang::T("UPLOAD_NO_DESC");
                    $langid = (int) $_POST["lang"];
                    preg_match('/^(.+)\.torrent$/si', $fname, $matches);
                    $shortfname = $torrent = $matches[1];

                    //parse torrent file
                    $torrent_dir = TORRENTDIR;
                    $torInfo = new Parse();
                    $tor = $torInfo->torr("$dir/$fname");

                    $announce = strtolower($tor[0]);
                    $infohash = $tor[1];
                    $creationdate = $tor[2];
                    $internalname = $tor[3];
                    $torrentsize = $tor[4];
                    $filecount = $tor[5];
                    $annlist = $tor[6];
                    $comment = $tor[7];

                    $message = "<br /><br /><hr /><br /><b>$internalname</b><br /><br />fname: " . htmlspecialchars($fname) . "<br />message: ";
                    //check announce url is local or external
                    if (!in_array($announce, $announce_urls, 1)) {
                        $external = 'yes';
                    } else {
                        $external = 'no';
                    }

                    if (!ALLOWEXTERNAL && $external == 'yes') {
                        $message .= Lang::T("UPLOAD_NO_TRACKER_ANNOUNCE");
                        echo $message;
                        continue;
                    }

                    $name = $internalname;
                    $name = str_replace(".torrent", "", $name);
                    $name = str_replace("_", " ", $name);

                    //anonymous upload
                    $anonyupload = $_POST["anonycheck"];
                    if ($anonyupload == "yes") {
                        $anon = "yes";
                    } else {
                        $anon = "no";
                    }

                    $ret = DB::run("INSERT INTO torrents (filename, owner, name, descr, category, added, info_hash, size, numfiles, save_as, announce, external, torrentlang, anon, last_action) VALUES (" . sqlesc($fname) . ", '" . $_SESSION['id'] . "', " . sqlesc($name) . ", " . sqlesc($descr) . ", '" . $catid . "', '" . TimeDate::get_date_time() . "', '" . $infohash . "', '" . $torrentsize . "', '" . $filecount . "', " . sqlesc($fname) . ", '" . $announce . "', '" . $external . "', '" . $langid . "','$anon', '" . TimeDate::get_date_time() . "')");
                    $id = DB::lastInsertId();

                    if ($ret->errorCode() == 1062) {
                        $message .= Lang::T("UPLOAD_ALREADY_UPLOADED");
                        echo $message;
                        continue;
                    }

                    if ($id == 0) {
                        $message .= Lang::T("UPLOAD_NO_ID");
                        echo $message;
                        continue;
                    }

                    copy("$dir/$files[$i]", "$torrent_dir/$id.torrent");

                    //EXTERNAL SCRAPE
                    if ($external == 'yes' && UPLOADSCRAPE) {
                        $torrent = new Torrent(TORRENTDIR . "/$id.torrent");
                        try {
                            $scraped = $torrent->scrape();
                        } catch (Exception $e) {
                            $scraped = $torrent->errors();
                            exit();
                        }
                        $myarray = array_shift($scraped);

                        $seeders = $leechers = $completed = 0;
                        if ($myarray['seeders'] > 0) {
                            $seeders = $myarray['seeders'];
                        }
                        if ($myarray['leechers'] > 0) {
                            $leechers = $myarray['leechers'];
                        }
                        if ($myarray['completed'] > 0) {
                            $completed = $myarray['completed'];
                        }
                        if ($seeders !== null) {
                            // Update the Torrent
                            DB::run("
                            UPDATE torrents
                            SET leechers = ?, seeders = ?, times_completed = ?, last_action = ?, visible = ?
                            WHERE id = ?",
                                [$leechers, $seeders, $completed, TimeDate::get_date_time(), 'yes', $id]
                            );
                        }
                    }

                    Logs::write("Torrent $id ($name) was Uploaded by $_SESSION[username]");
                    $message .= "<br /><b>" . Lang::T("UPLOAD_OK") . "</b><br /><a href='" . URLROOT . "/torrent?id=" . $id . "'>" . Lang::T("UPLOAD_VIEW_DL") . "</a><br /><br />";
                    echo $message;
                    @unlink("$dir/$fname");
                }
                echo "</center>";
                Style::end();
                Style::footer();
                die;
            } else {
                show_error_msg(Lang::T("UPLOAD_FAILED"), $message, 1);
            }

        }

        Style::header(Lang::T("UPLOAD"));
        Style::begin(Lang::T("UPLOAD"));
        include APPROOT . "/views/torrent/import.php";
        Style::end();
        Style::footer();
    }

}