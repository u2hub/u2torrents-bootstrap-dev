<?php echo $data['message']; ?>
<table align='center' class='table_table'>
<tr>
<th class='table_head'>No.</th>
<th class='table_head'><?php echo Lang::T("USERNAME") ?></th>
<th class='table_head'><?php echo Lang::T("UPLOADED") ?></th>
<th class='table_head'><?php echo Lang::T("DOWNLOADED") ?></th>
<th class='table_head'><?php echo Lang::T("RATIO") ?></th>
<th class='table_head'><?php echo Lang::T("TORRENTS_POSTED") ?></th>
<th class='table_head'>AVG Daily Upload</th>
<th class='table_head'><?php echo Lang::T("ACCOUNT_SEND_MSG") ?></th>
<th class='table_head'>Joined</th>
</tr>
<?php
for ($i = 0; $i <= $data['zerofix']; $i++) {
    $id = $data['result']->fetch($i, "id");
    $username = $data['result']->fetch($i, "username");
    $added = $data['result']->fetch($i, "added");
    $uploaded = $data['result']->fetch($i, "uploaded");
    $downloaded = $data['result']->fetch($i, "downloaded");
    $donated = $data['result']->fetch($i, "donated");
    $warned = $data['result']->fetch($i, "warned");
    $joindate = "" . TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($added)) . " ago";
    $upperresult = DB::run("SELECT added FROM torrents WHERE owner =?", [$id]);
    $seconds = TimeDate::mkprettytime(TimeDate::utc_to_tz_time() - TimeDate::utc_to_tz_time($added));
    $days = explode("d ", $seconds);
    if (sizeof($days) > 1) {
        $dayUpload = $uploaded / $days[0];
        $dayDownload = $downloaded / $days[0];
    }
        $torrentinfo = $upperresult->fetch(PDO::FETCH_LAZY);
        $numtorrents = $upperresult->rowCount();
    if ($downloaded > 0) {
        $ratio = $uploaded / $downloaded;
        $ratio = number_format($ratio, 3);
        $color = get_ratio_color($ratio);
        if ($color) {
            $ratio = "<font color='$color'>$ratio</font>";
        }
    } else
        if ($uploaded > 0) {
            $ratio = "Inf.";
        } else {
            $ratio = "---";
        }
        $counter = $i + 1;
        echo "<tr>";
        echo "<td align='center class='table_col1'>$counter.</td>";
        echo "<td class='table_col2'><a href='" . URLROOT . "/profile?id=$id'>$username</a></td>";
        echo "<td class='table_col1'>" . mksize($uploaded) . "</td>";
        echo "<td class='table_col2'>" . mksize($downloaded) . "</td>";
        echo "<td class='table_col1'>$ratio</td>";
        if ($numtorrents == 0) {
            echo "<td class='table_col2'><font color='red'>$numtorrents torrents</font></td>";
        } else {
            echo "<td class=table_col2>$numtorrents torrents</td>";
        }
        echo "<td class='table_col1'>" . mksize($dayUpload) . "</td>";
        echo "<td align='center' class='table_col2'><a href='messages/create?id=$id'>PM</a></td>";
        echo "<td class='table_col1'>" . $joindate . "</td>";
        echo "</tr>";

}
echo "</table><br /><br />";