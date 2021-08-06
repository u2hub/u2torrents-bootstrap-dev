<?php
if (!MEMBERSONLY || $_SESSION['loggedin'] == true) {
    $limit = 25; // Only show 25 max

    $res = DB::run("SELECT torrents.id, torrents.name, torrents.image1, torrents.image2, categories.name as cat_name, categories.parent_cat as cat_parent FROM torrents LEFT JOIN categories ON torrents.category=categories.id WHERE banned = 'no' AND (image1 != '' OR image2 != '') AND visible = 'yes' ORDER BY id DESC LIMIT $limit");
    if ($res->rowCount() > 0) {
        Style::block_begin(Lang::T("LATEST_POSTERS"));
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $cat = htmlspecialchars("$row[cat_parent] - $row[cat_name]");
            $name = htmlspecialchars($row["name"]);

            if ($row["image1"]) {?>
					<center><div class="col-lg-6"><a href="<?php echo URLROOT; ?>/torrent?id=<?php echo $row["id"]; ?>" title="<?php echo $name . " / " . $cat; ?>"><img src="<?php echo data_uri(TORRENTDIR."/assets/images/".$row["image1"], $row['image1']); ?>" alt="<?php echo $name . " / " . $cat; ?>" class="img-thumbnail" /></a></div></center>
				<?php } else {?>
					<center><div class="col-lg-6"><a href="<?php echo URLROOT; ?>/torrent?id=<?php echo $row["id"]; ?>" title="<?php echo $name . " / " . $cat; ?>"><img src="<?php echo data_uri(TORRENTDIR."/assets/images/".$row["image2"], $row['image2']); ?>" alt="<?php echo $name . " / " . $cat; ?>" class="img-thumbnail" /></a></div></center>
				<?php }
        }
?>
        <!-- end content -->

 <?php Style::block_end();
 }
}