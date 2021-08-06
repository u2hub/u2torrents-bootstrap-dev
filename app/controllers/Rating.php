<?php
class Rating
{
    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        $id = (int) Input::get("id");
        if (!Validate::Id($id)) {
            Redirect::autolink(URLROOT . "//torrent?id=$id", Lang::T("THATS_NOT_A_VALID_ID"));
        }
        if (Input::get("takerating") == 'yes') {
            $rating = (int) Input::get('rating');
            if ($rating <= 0 || $rating > 5) {
                Redirect::autolink(URLROOT . "//torrent?id=$id", Lang::T("INVAILD_RATING"));
            }
            $res = DB::run("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $_SESSION["id"] . ", $rating, '" . TimeDate::get_date_time() . "')");
            if (!$res) {
                if ($res->errorCode() == 1062) {
                    Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("YOU_ALREADY_RATED_TORRENT"));
                } else {
                    Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("A_UNKNOWN_ERROR_CONTACT_STAFF"));
                }
            }
            DB::run("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
            Redirect::autolink(URLROOT . "/torrent?id=$id", Lang::T("RATING_THANK") . "<br /><br /><a href='" . URLROOT . "/torrent?id=$id'>" . Lang::T("BACK_TO_TORRENT") . "</a>");
        }
    }

}