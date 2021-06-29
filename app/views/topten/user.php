<?php
Style::begin($title);
?>
<div class='table-responsive'><table class='table table-striped'>
   <thead><tr>
    <th>Rank</td>
    <th>User</td>
    <th>Uploaded</td>
    <th>UL speed</td>
    <th>Downloaded</td>
    <th>DL speed</td>
    <th>Ratio</td>
    <th>Joined</td>
    </tr></thead>
    <?php
    $num = 0;
    while ($a = $res->fetch(PDO::FETCH_ASSOC)) {
        ++$num;
        $highlight = $_SESSION["id"] == $a["userid"] ? " bgcolor=#fff" : "";
        if ($a["downloaded"]) {
            $ratio = $a["uploaded"] / $a["downloaded"];
            $color = get_ratio_color($ratio);
            $ratio = number_format($ratio, 2);
            if ($color) {
                $ratio = "<font color=#ffff00>$ratio</font>";
            }
        } else {
            $ratio = "Inf.";
        }
        
        print("<tbody><tr>
            <td>$num</td>
            <td><a href=".URLROOT."/profile?id=" . $a["userid"] . "><b>" . Users::coloredname($a["username"]) . "</b>" . "</td>
            <td><font color=limegreen>" . mksize($a["uploaded"]) . "</font></td>
            <td><font color=limegreen>" . mksize($a["upspeed"]) . "/s" . "</font></td>
            <td><font color=#FF2400>" . mksize($a["downloaded"]) . "</font></td>
            <td><font color=#FF2400>" . mksize($a["downspeed"]) . "/s" . "</font></td>
            <td>" . $ratio . "</td>
            <td>" . gmdate("d-M-Y", strtotime($a["added"])) . " (" . TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($a["added"])) . " ago)</td>
            </tr><tbody>");
    }
print("</table></div>");
Style::end();