<?php
class Admintask
{

    public function __construct()
    {
        $this->session = Auth::user(_MODERATOR, 2);
    }

    public function index()
    {
        Redirect::to(URLROOT . '/admincp');
    }

    public function cleanup()
    {
        $now = TimeDate::gmtime();
        DB::run("UPDATE tasks SET last_time=$now WHERE task='cleanup'");
        Cleanup::run();
        Redirect::autolink(URLROOT . '/admincp', Lang::T("FORCE_CLEAN_COMPLETED"));
    }

    public function cache()
    {
        # Prune Block Cache.
        $TTCache = new Cache();
        $TTCache->Delete("blocks_left");
        $TTCache->Delete("blocks_middle");
        $TTCache->Delete("blocks_right");
        $TTCache->Delete("latestuploadsblock");
        $TTCache->Delete("mostactivetorrents_block");
        $TTCache->Delete("newestmember_block");
        $TTCache->Delete("seedwanted_block");
        $TTCache->Delete("usersonline_block");
        $TTCache->Delete("request_block");
        Redirect::autolink(URLROOT . "/admincp", 'Purge Cache Successful');
    }

}