<?php
class Scrape extends Controller
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
        // check if client can handle gzip
        if (stristr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") && extension_loaded('zlib') && ini_get("zlib.output_compression") == 0) {
            if (ini_get('output_handler') != 'ob_gzhandler') {
                ob_start("ob_gzhandler");
            } else {
                ob_start();
            }
        } else {
            ob_start();
        }

        $infohash = array();
        foreach (explode("&", $_SERVER["QUERY_STRING"]) as $item) {
            if (preg_match("#^info_hash=(.+)\$#", $item, $m)) {
                $hash = urldecode($m[1]);
                $info_hash = stripslashes($hash);
                if (strlen($info_hash) == 20) {
                    $info_hash = bin2hex($info_hash);
                } elseif (strlen($info_hash) != 40) {
                    continue;
                }
                $infohash[] = sqlesc(strtolower($info_hash));
            }
        }

        if (!count($infohash)) {
            die("Invalid infohash.");
        }
        $query = DB::run("SELECT info_hash, seeders, leechers, times_completed, filename FROM torrents WHERE info_hash IN (" . join(",", $infohash) . ")");
        $result = "d5:filesd";

        while ($row = $query->fetch()) {
            $hash = pack("H*", $row[0]);
            $result .= "20:" . $hash . "d";
            $result .= "8:completei" . $row[1] . "e";
            $result .= "10:downloadedi" . $row[3] . "e";
            $result .= "10:incompletei" . $row[2] . "e";
            $result .= "4:name" . strlen($row[4]) . ":" . $row[4] . "e";
            $result .= "e";
        }

        $result .= "ee";
        echo $result;
        ob_end_flush();
    }

    public function external()
    {
        $id = $_GET['id'];
        $resu = DB::run("SELECT id, info_hash FROM torrents WHERE external = 'yes' AND id = $id");
        while ($rowu = $resu->fetch(PDO::FETCH_ASSOC)) {
            $torrent = new Torrent(TORRENTDIR . "/$rowu[id].torrent");
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
                [$leechers, $seeders, $completed, TimeDate::get_date_time(), 'yes', $rowu['id']]);

            } else {
                // Its Dead :(
                DB::run("UPDATE torrents SET last_action = ? WHERE id=?", [TimeDate::get_date_time(), $rowu['id']]);
            }

            // Redirect with message
            if ($seeders !== null) {
                Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("The Tracker is Updated"));
            } else {
                Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("The Torrent seems to be dead"));
            }
        }
    }
}