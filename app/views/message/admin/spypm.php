<?php echo $data['pagertop']; ?>
<form id='messagespy' method='post' action='adminmessages/delete'>
<table class='table table-striped table-bordered table-hover'><thead>
<tr><th class='table_head' align='left'><input type='checkbox' name='checkall' onclick='checkAll(this.form.id);' /></th>
<th class='table_head' align='left'>Sender</th>
<th class='table_head' align='left'>Receiver</th>
<th class='table_head' align='left'>Text</th><th class='table_head' align='left'>Date</th>
</tr></thead><tbody> <?php
while ($arr = $data['res']->fetch(PDO::FETCH_ASSOC)) {
    $res2 = DB::run("SELECT username FROM users WHERE id=?", [$arr["receiver"]]);
    if ($arr2 = $res2->fetch()) {
        $receiver = "<a href='" . URLROOT . "/profile?id=" . $arr["receiver"] . "'><b>" . Users::coloredname($arr2["username"]) . "</b></a>";
    } else {
        $receiver = "<i>Deleted</i>";
    }
    $arr3 = DB::run("SELECT username FROM users WHERE id=?", [$arr["sender"]])->fetch();
    $sender = "<a href='" . URLROOT . "/profile?id=" . $arr["sender"] . "'><b>" . Users::coloredname($arr3["username"]) . "</b></a>";
    if ($arr["sender"] == 0) {
        $sender = "<font class='error'><b>System</b></font>";
    }
    $msg = format_comment($arr["msg"]);
    $added = TimeDate::utc_to_tz($arr["added"]);
    print("<tr><td class='table_col2'><input type='checkbox' name='del[]' value='$arr[id]' /></td><td align='left' class='table_col1'>$sender</td><td align='left' class='table_col2'>$receiver</td><td align='left' class='table_col1'>$msg</td><td align='left' class='table_col2'>$added</td></tr>");
} ?>
</tbody></table><br />
<center>
<input type='submit' value='Delete Checked' /> 
<input type='submit' value='Delete All' name='delall' />
</center>
<?php echo $data['pagerbottom']; ?>