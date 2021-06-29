<?php
Style::begin($title);
?>
<div class='table-responsive'><table class='table table-striped'>
    <thead><tr>
    <th>Rank</th>
    <th>Name</th>
    <th><img src="<?php echo URLROOT; ?>/assets/images/check.png" border=0 title=Completed></th>
    <th>Traffic</th>
    <th><img src="<?php echo URLROOT; ?>/assets/images/seed.gif" border=0 title=Seeders></th>
    <th><img src="<?php echo URLROOT; ?>/assets/images/leech.gif" border=0 title=Leechers></th>
    <th>Total</th>
    <th>Ratio</th>
    </tr></thead>
    <?php
    $num = 0;
    while ($a = $res->fetch(PDO::FETCH_ASSOC)) {
        ++$num;
        if ($a["leechers"]) {
            $r = $a["seeders"] / $a["leechers"];
            $ratio = "<font color=#ff9900>" . number_format($r, 2) . "</font>";
        } else {
            $ratio = "Inf.";
        }
        print("<tbody><tr>
            <td>$num</td>
            <td><a href=".URLROOT."/torrent?id=" . $a["id"] . "&hit=1><b>" . $a["name"] . "</b></a></td>
            <td><font color=#0080FF><b>" . number_format($a["times_completed"]) . "</b></font></td>
            <td>" . mksize($a["data"]) . "</td>
            <td><font color=limegreen><b>" . number_format($a["seeders"]) . "</b></font></td>
            <td><font color=red><b>" . number_format($a["leechers"]) . "</b></font></td>
            <td>" . ($a["leechers"] + $a["seeders"]) . "</td>
            <td>$ratio</td>
             </tr><tbody>");
    }
print("</table></div>");
Style::end();