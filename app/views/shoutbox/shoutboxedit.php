<?php

echo "<form name='shoutboxform' action='shoutbox?id=$edit' method='post'>";
echo "<center><table width='100%' border='0' cellpadding='1' cellspacing='1'>";
echo "<tr class='shoutbox_messageboxback'>";
$result = DB::run("SELECT message FROM shoutbox WHERE msgid=?", [$edit])->fetchColumn();

echo "<td width='75%' align='center'>";

?><input type="text" name="update" class='shoutbox_msgbox' value="<?php echo $result; ?>" /><?php
//echo "<input type='text' name='message' class='shoutbox_msgbox' value='$result'></input>";

echo "</td>";

echo "<td>";
echo "<input type='submit' name='submit' class='btn btn-sm btn-primary' />";
echo "</td>";
echo "<td>";
echo '<a href="javascript:PopMoreSmiles(\'shoutboxform\', \'message\');"><small>' . Lang::T("Smilies") . '</small></a>';
echo ' <small>-</small> <a href="javascript:PopMoreTags();"><small>' . Lang::T("TAGS") . '</small></a>';
//echo "<br />";
echo "<small>-</small> <a href='shoutbox'><small>" . Lang::T("REFRESH") . "</small></a>";
echo " <small>-</small> <a href='" . URLROOT . "/shoutbox?history=1' target='_blank'><small>" . Lang::T("HISTORY") . "</small></a>";
echo "</td>";
echo "</tr>";
echo "</table></center>";
echo "</form>";