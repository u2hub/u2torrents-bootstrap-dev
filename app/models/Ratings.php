<?php
class Ratings
{

    public static function ratingtor($id)
    {
        $xres = DB::run("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $_SESSION["id"]);
        $xrow = $xres->fetch(PDO::FETCH_ASSOC);
        $srating = "";
        $srating .= "<br><b>" . Lang::T("RATINGS") . ":</b><br>";
        if (!isset($xrow["rating"])) {
            $srating .= "<br><b>Not Yet Rated</b><br>";
        } else {
            $rpic = ratingpic($xrow["rating"]);
            if (!isset($rpic)) {
                $srating .= "invalid?";
            } else {
                $srating .= "$rpic (" . $xrow["rating"] . " " . Lang::T("OUT_OF") . " 5) " . $xrow["numratings"] . " " . Lang::T("USERS_HAVE_RATED");
            }

        }
        $ratings = array(
            5 => Lang::T("COOL"),
            4 => Lang::T("PRETTY_GOOD"),
            3 => Lang::T("DECENT"),
            2 => Lang::T("PRETTY_BAD"),
            1 => Lang::T("SUCKS"),
        );
        $xres = DB::run("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $_SESSION["id"]);
        $xrow = $xres->fetch(PDO::FETCH_ASSOC);
        if ($xrow) {
            $srating .= "<br /><i>(" . Lang::T("YOU_RATED") . " \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")</i>";
        } else {
            $srating .= "<form style=\"display:inline;\" method=\"post\" action=\"".URLROOT."/rating?id=$id&takerating=yes\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
            $srating .= "<select name=\"rating\">\n";
            $srating .= "<option value=\"0\">(" . Lang::T("ADD_RATING") . ")</option>\n";
            foreach ($ratings as $k => $v) {
                $srating .= "<option value=\"$k\">$k - $v</option>\n";
            }
            $srating .= "</select>\n";
            $srating .= "<input type=\"submit\" value=\"" . Lang::T("VOTE") . "\" />";
            $srating .= "</form>\n";
        }

        return $srating; // rating
    }
    
}