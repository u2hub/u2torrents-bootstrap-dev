                   <form name="upload" enctype="multipart/form-data" action="import" method="post">
                   <input type="hidden" name="takeupload" value="yes" />
                   <table border="0" cellspacing="0" cellpadding="6" align="center">
                   <tr><td align="right" valign="top"><b>File List:</b></td><td align="left"><?php
if (!count($files)) {
    echo Lang::T("NOTHING_TO_SHOW_FILES") . " $dir/.";
} else {
    foreach ($files as $f) {
        echo htmlspecialchars($f) . "<br />";
    }

    echo "<br />Total files: " . count($files);
}?></td></tr>
                   <?php
$category = "<select name=\"type\">\n<option value=\"0\">" . Lang::T("CHOOSE_ONE") . "</option>\n";

$cats = genrelist();
foreach ($cats as $row) {
    $category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";
}

$category .= "</select>\n";
print("<tr><td align='right'>" . Lang::T("CATEGORY") . ": </td><td align='left'>" . $category . "</td></tr>");

$language = "<select name=\"lang\">\n<option value=\"0\">Unknown/NA</option>\n";

$langs = langlist();
foreach ($langs as $row) {
    $language .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";
}

$language .= "</select>\n";
print("<tr><td align='right'>Language: </td><td align='left'>" . $language . "</td></tr>");
$anonycheck = '';
if (ANONYMOUSUPLOAD) {?>
                       <tr><td align="right"><?php echo Lang::T("UPLOAD_ANONY"); ?>: </td><td><?php printf("<input name='anonycheck' value='yes' type='radio' " . ($anonycheck ? " checked='checked'" : "") . " />Yes <input name='anonycheck' value='no' type='radio' " . (!$anonycheck ? " checked='checked'" : "") . " />No");?> &nbsp;<?php echo Lang::T("UPLOAD_ANONY_MSG"); ?>
                       </td></tr>

                   <?php }?>
                   <tr><td align="center" colspan="2"><button type="submit" class="btn ttbtn btn-sm"><?php echo Lang::T("UPLOAD"); ?></button><br />
                   <i><?php echo Lang::T("CLICK_ONCE_IMAGE"); ?></i></td></tr></table></form>