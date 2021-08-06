<?php
if ($_SESSION['loggedin'] == true) {
    Style::block_begin(Lang::T("SEEDERS_WANTED"));
    $external = "external = 'no'";
    // Uncomment below to include external torrents
    $external = 1;
    $TTCache = new Cache();
    $expires = 600; // Cache time in seconds
    if (($rows = $TTCache->Get("seedwanted_block", $expires)) === false) {
        $res = DB::run("SELECT id, name, seeders, leechers FROM torrents WHERE seeders = ? AND leechers > ? AND banned = ? AND ? ORDER BY leechers DESC LIMIT 5", [0, 0, 'no', $external]);
        $rows = array();

        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }

        $TTCache->Set("seedwanted_block", $rows, $expires);
    }

    if (!$rows) {?>
		<p class="text-center"><?php echo Lang::T("NOTHING_FOUND"); ?></p>
	<?php } else {
        foreach ($rows as $row) {
            $char1 = 20; //cut length
            $smallname = htmlspecialchars(CutName($row["name"], $char1));?>

			<div class="pull-left"><a href="<?php echo URLROOT; ?>torrent?id=<?php echo $row["id"]; ?>" title="<?php echo htmlspecialchars($row["name"]); ?>"><?php echo $smallname; ?></a></div>
			<div class="pull-right"><span class="label label-waring"><?php echo Lang::T("LEECHERS"); ?>: <?php echo number_format($row["leechers"]); ?></span></div>
		<?php }
    }
    Style::block_end();
}