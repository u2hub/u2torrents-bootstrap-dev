<?php

class Search
{

    public function __construct()
    {
        $this->session = Auth::user(0, 2);
    }

    public function index()
    {
        //check permissions
        if (MEMBERSONLY) {
            if ($_SESSION["view_torrents"] == "no") {
                Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
            }
        }

        //GET SEARCH STRING
        $searchstr = trim($_GET["search"] ?? '');
        $cleansearchstr = searchfield($searchstr);
        if (empty($cleansearchstr)) {
            unset($cleansearchstr);
        }

        $thisurl = "search?";
        $addparam = "";
        $wherea = array();
        $wherecatina = array();
        $wherea[] = "banned = 'no'";

        $wherecatina = array();
        $wherecatin = "";
        $res = Torrents::getCatById();
        while ($row = $res->fetch(PDO::FETCH_LAZY)) {
            if (isset($_GET["c$row[id]"])) {
                $wherecatina[] = $row['id'];
                $addparam .= "c$row[id]=1&amp;";
                $addparam .= "c$row[id]=1&amp;";
                $thisurl .= "c$row[id]=1&amp;";
            }
            $wherecatin = implode(", ", $wherecatina);
        }
        if ($wherecatin) {
            $wherea[] = "category IN ($wherecatin)";
        }

        $_GET['incldead'] = (int) ($_GET['incldead'] ?? 0);
        $_GET['freeleech'] = (int) ($_GET['freeleech'] ?? 0);
        $_GET['inclexternal'] = (int) ($_GET['inclexternal'] ?? 0);
        $_GET['cat'] = $_GET['cat'] ?? '';

        //include dead
        if ($_GET["incldead"] == 1) {
            $addparam .= "incldead=1&amp;";
            $thisurl .= "incldead=1&amp;";
        } elseif ($_GET["incldead"] == 2) {
            $wherea[] = "visible = 'no'";
            $addparam .= "incldead=2&amp;";
            $thisurl .= "incldead=2&amp;";
        } else {
            $wherea[] = "visible = 'yes'";
        }

        // Include freeleech
        if ($_GET["freeleech"] == 1) {
            $addparam .= "freeleech=1&amp;";
            $thisurl .= "freeleech=1&amp;";
            $wherea[] = "freeleech = '0'";
        } elseif ($_GET["freeleech"] == 2) {
            $addparam .= "freeleech=2&amp;";
            $thisurl .= "freeleech=2&amp;";
            $wherea[] = "freeleech = '1'";
        }

        //include external
        if ($_GET["inclexternal"] == 1) {
            $addparam .= "inclexternal=1&amp;";
            $wherea[] = "external = 'no'";
        }

        if ($_GET["inclexternal"] == 2) {
            $addparam .= "inclexternal=2&amp;";
            $wherea[] = "external = 'yes'";
        }

        //cat
        if ($_GET["cat"]) {
            $wherea[] = "category = " . sqlesc($_GET["cat"]);
            $wherecatina[] = sqlesc($_GET["cat"]);
            $addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
            $thisurl .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
        }

        //language
        if ($_GET["lang"] ?? '') {
            $wherea[] = "torrentlang = " . sqlesc($_GET["lang"]);
            $addparam .= "lang=" . urlencode($_GET["lang"]) . "&amp;";
            $thisurl .= "lang=" . urlencode($_GET["lang"]) . "&amp;";
        }

        //parent cat
        if ($_GET["parent_cat"] ?? '') {
            $addparam .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
            $thisurl .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
        }

        $parent_cat = $_GET["parent_cat"] ?? '';

        $wherebase = $wherea;

        if (isset($cleansearchstr)) {
            $wherea[] = "MATCH (torrents.name) AGAINST ('" . $searchstr . "' IN BOOLEAN MODE)";

            $addparam .= "search=" . urlencode($searchstr) . "&amp;";
            $thisurl .= "search=" . urlencode($searchstr) . "&amp;";
        }

        //order by
        if ($_GET['sort'] ?? '' && $_GET['order']) {
            $column = '';
            $ascdesc = '';
            switch ($_GET['sort']) {
                case 'id':$column = "id";
                    break;
                case 'name':$column = "name";
                    break;
                case 'comments':$column = "comments";
                    break;
                case 'size':$column = "size";
                    break;
                case 'times_completed':$column = "times_completed";
                    break;
                case 'seeders':$column = "seeders";
                    break;
                case 'leechers':$column = "leechers";
                    break;
                case 'category':$column = "category";
                    break;
                default:$column = "id";
                    break;
            }

            switch ($_GET['order']) {
                case 'asc':$ascdesc = "ASC";
                    break;
                case 'desc':$ascdesc = "DESC";
                    break;
                default:$ascdesc = "DESC";
                    break;
            }
        } else {
            $_GET["sort"] = "id";
            $_GET["order"] = "desc";
            $column = "id";
            $ascdesc = "DESC";
        }

        $orderby = "ORDER BY torrents." . $column . " " . $ascdesc;
        $pagerlink = "sort=" . $_GET['sort'] . "&amp;order=" . $_GET['order'] . "&amp;";

        if (Validate::Id($_GET["page"] ?? '')) {
            $thisurl .= "page=$_GET[page]&amp;";
        }

        $where = implode(" AND ", $wherea);

        if ($where != "") {
            $where = "WHERE $where";
        }

        $parent_check = "";
        if ($parent_cat) {
            $parent_check = " AND categories.parent_cat=" . sqlesc($parent_cat);
        }

        //GET NUMBER FOUND FOR PAGER
        $count = Torrents::getTorrentWhere($where, $parent_check);

        if (!$count && isset($cleansearchstr)) {
            $wherea = $wherebase;
            $searcha = explode(" ", $cleansearchstr);
            $sc = 0;
            foreach ($searcha as $searchss) {
                if (strlen($searchss) <= 1) {
                    continue;
                }

                $sc++;
                if ($sc > 5) {
                    break;
                }

                $ssa = array();
                foreach (array("torrents.name") as $sss) {
                    //$ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
                    $ssa[] = "$sss LIKE '%" . $searchss . "%'";
                }

                $wherea[] = "(" . implode(" OR ", $ssa) . ")";
            }
            if ($sc) {
                $where = implode(" AND ", $wherea);
                if ($where != "") {
                    $where = "WHERE $where";
                }

                $row = DB::run("SELECT COUNT(*) FROM torrents $where $parent_check")->fetch();
                $count = $row[0];
            }
        }

        //Sort by
        if ($addparam != "") {
            if ($pagerlink != "") {
                if ($addparam[strlen($addparam) - 1] != ";") { // & = &amp;
                    $addparam = $addparam . "&amp;" . $pagerlink;
                } else {
                    $addparam = $addparam . $pagerlink;
                }
            }
        } else {
            $addparam = $pagerlink;
        }

        if ($count) {

            //SEARCH QUERIES!
            list($pagertop, $pagerbottom, $limit) = pager(20, $count, "search?" . $addparam);
            $res = Torrents::getTorrentByCat($where, $parent_check, $orderby, $limit);

        } else {
            unset($res);
        }

        if (isset($cleansearchstr)) {
            Style::header(Lang::T("SEARCH_RESULTS_FOR") . " \"" . htmlspecialchars($searchstr) . "\"");
        } else {
            Style::header(Lang::T("BROWSE_TORRENTS"));
        }

        Style::begin(Lang::T("SEARCH_TORRENTS"));

        // get all parent cats
        echo "<center><b>" . Lang::T("CATEGORIES") . ":</b> ";
        $catsquery = Torrents::getCatByParent();
        echo " - <a href='" . URLROOT . "/search/browse'>" . Lang::T("SHOWALL") . "</a>";
        while ($catsrow = $catsquery->fetch(PDO::FETCH_ASSOC)) {
            echo " - <a href='" . URLROOT . "/search/browse?parent_cat=" . urlencode($catsrow['parent_cat']) . "'>$catsrow[parent_cat]</a>";
        }
        echo "</center>";

        ?>
            <br /><br />

            <center>
            <form method="get" action="<?php echo URLROOT; ?>/search">
            <table border="0" align="center">
            <tr align='right'>
            <?php
        $i = 0;
        $cats = Torrents::getCatByParentName();
        while ($cat = $cats->fetch(PDO::FETCH_ASSOC)) {
            $catsperrow = 5;
            print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
            print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a href=" . URLROOT . "/search/browse?cat={$cat["id"]}'>" . htmlspecialchars($cat["parent_cat"]) . " - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) || $_GET["cat"] == $cat["id"] ? "checked='checked' " : "") . "value='1' /></td>\n");
            $i++;
        }
        echo "</tr></table>";

        //if we are browsing, display all subcats that are in same cat
        if ($parent_cat) {
            echo "<br /><br /><b>" . Lang::T("YOU_ARE_IN") . ":</b> <a href=" . URLROOT . "/search/browse?parent_cat=$parent_cat'>$parent_cat</a><br /><b>" . Lang::T("SUB_CATS") . ":</b> ";
            $subcatsquery = Torrents::getSubCatByParentName($parent_cat);
            while ($subcatsrow = $subcatsquery->fetch(PDO::FETCH_ASSOC)) {
                $name = $subcatsrow['name'];
                echo " - <a href=" . URLROOT . "/search/browse?cat=$subcatsrow[id]'>$name</a>";
            }
        }

        echo "<br /><br />"; //some spacing
        ?>
                <?php print(Lang::T("SEARCH"));?>
                <input type="text" name="search" size="40" value="<?php echo stripslashes(htmlspecialchars($searchstr)) ?>" />
                <?php print(Lang::T("IN"));?>
                <select name="cat">
                <option value="0"><?php echo "(" . Lang::T("ALL") . " " . Lang::T("TYPES") . ")"; ?></option>
                <?php

        $cats = genrelist();
        $catdropdown = "";
        foreach ($cats as $cat) {
            $catdropdown .= "<option value=\"" . $cat["id"] . "\"";
            if ($cat["id"] == $_GET["cat"]) {
                $catdropdown .= " selected=\"selected\"";
            }
            $catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
        }
        ?>
                <?php echo $catdropdown ?>
                </select>
                <br /><br />
                <select name="incldead">
                 <option value="0"><?php echo Lang::T("ACTIVE_TRANSFERS"); ?></option>
                <option value="1" <?php if ($_GET["incldead"] == 1) {
            echo "selected='selected'";
        }
        ?>><?php echo Lang::T("INC_DEAD"); ?></option>
                <option value="2" <?php if ($_GET["incldead"] == 2) {
            echo "selected='selected'";
        }
        ?>><?php echo Lang::T("ONLY_DEAD"); ?></option>
                </select>
                <select name="freeleech">
                <option value="0"><?php echo Lang::T("ALL"); ?></option>
                <option value="1" <?php if ($_GET["freeleech"] == 1) {
            echo "selected='selected'";
        }
        ?>><?php echo Lang::T("NOT_FREELEECH"); ?></option>
                <option value="2" <?php if ($_GET["freeleech"] == 2) {
            echo "selected='selected'";
        }
        ?>><?php echo Lang::T("ONLY_FREELEECH"); ?></option>
                 </select>

                <?php if (ALLOWEXTERNAL) {?>
                    <select name="inclexternal">
                     <option value="0"><?php echo Lang::T("LOCAL_EXTERNAL"); ?></option>
                    <option value="1" <?php if ($_GET["inclexternal"] == 1) {
            echo "selected='selected'";
        }
            ?>><?php echo Lang::T("LOCAL_ONLY"); ?></option>
                    <option value="2" <?php if ($_GET["inclexternal"] == 2) {
                echo "selected='selected'";
            }
            ?>><?php echo Lang::T("EXTERNAL_ONLY"); ?></option>
                     </select>
                <?php }?>

                <select name="lang">
                <option value="0"><?php echo "(" . Lang::T("ALL") . ")"; ?></option>
                <?php
        $lang = langlist();
        $langdropdown = "";
        foreach ($lang as $lang) {
            $langdropdown .= "<option value=\"" . $lang["id"] . "\"";
            if ($lang["id"] == $_GET["lang"]) {
                $langdropdown .= " selected=\"selected\"";
            }
            $langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
        }
        ?>
                <?php echo $langdropdown ?>
                </select>
                <button type='submit' class='btn btn-sm ttbtn'><?php print Lang::T("SEARCH");?></button>
                <br />
                </form>
                <?php print Lang::T("SEARCH_RULES");?><br />
                </center>
            <?php
        if ($count) {
            // New code (TorrentialStorm)
            echo "<form id='sort' action=''><div align='right'>" . Lang::T("SORT_BY") . ": <select name='sort' onchange='window.location=\"{$thisurl}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
            echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">" . Lang::T("ADDED") . "</option>";
            echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">" . Lang::T("NAME") . "</option>";
            echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">" . Lang::T("COMMENTS") . "</option>";
            echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">" . Lang::T("SIZE") . "</option>";
            echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">" . Lang::T("COMPLETED") . "</option>";
            echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">" . Lang::T("SEEDERS") . "</option>";
            echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">" . Lang::T("LEECHERS") . "</option>";
            echo "</select>&nbsp;";
            echo "<select name='order' onchange='window.location=\"{$thisurl}order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
            echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">" . Lang::T("ASCEND") . "</option>";
            echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">" . Lang::T("DESCEND") . "</option>";
            echo "</select>";
            echo "</div>";
            echo "</form>";

            // End
            torrenttable($res);
            print($pagerbottom);
        } else {
            print(Lang::T("NOTHING_FOUND") . "&nbsp;&nbsp;");
            print Lang::T("NO_UPLOADS");
        }
        if ($_SESSION['loggedin'] == true) {
            DB::run("UPDATE users SET last_browse=" . TimeDate::gmtime() . " WHERE id=$_SESSION[id]");
        }
        Style::end();
        Style::footer();
    }

    public function today()
    {
        //check permissions
        if (MEMBERSONLY) {
            if ($_SESSION["view_torrents"] == "no") {
                Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
            }
        }

        $date_time = TimeDate::get_date_time(TimeDate::gmtime() - (3600 * 24)); // the 24 is the hours you want listed
        $catresult = Torrents::getCatSort();

        Style::header(Lang::T("TODAYS_TORRENTS"));
        Style::begin(Lang::T("TODAYS_TORRENTS"));
        while ($cat = $catresult->fetch(PDO::FETCH_ASSOC)) {
            $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC"; //Order
            $where = "WHERE banned = 'no' AND category='$cat[id]' AND visible='yes'";
            $limit = "LIMIT 10"; //Limit

            $res = Torrents::getCatSortAll($where, $date_time, $orderby, $limit);
            $numtor = $res->rowCount();
            if ($numtor != 0) {
                echo "<b><a href=" . URLROOT . "/torrent/browse?cat=" . $cat["id"] . "'>$cat[name]</a></b>";
                torrenttable($res);
                echo "<br />";
            }
        }
        Style::end();
        Style::footer();
    }

    public function browse()
    {
        //check permissions
        if (MEMBERSONLY) {
            if ($_SESSION["view_torrents"] == "no") {
                Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
            }
        }
        //get http vars
        $addparam = "";
        $wherea = array();
        $wherea[] = "visible = 'yes'";
        $thisurl = "search/browse?";

        if ($_GET["cat"]) {
            $wherea[] = "category = " . sqlesc($_GET["cat"]);
            $addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
            $thisurl .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
        }

        if ($_GET["parent_cat"]) {
            $addparam .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
            $thisurl .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
            $wherea[] = "categories.parent_cat=" . sqlesc($_GET["parent_cat"]);
        }

        $parent_cat = $_GET["parent_cat"];
        $category = (int) $_GET["cat"];

        $where = implode(" AND ", $wherea);
        $wherecatina = array();
        $wherecatin = "";
        $res = Torrents::getCatById();
        while ($row = $res->fetch(PDO::FETCH_LAZY)) {
            if ($_GET["c$row[id]"]) {
                $wherecatina[] = $row["id"];
                $addparam .= "c$row[id]=1&amp;";
                $thisurl .= "c$row[id]=1&amp;";
            }
            $wherecatin = implode(", ", $wherecatina);
        }

        if ($wherecatin) {
            $where .= ($where ? " AND " : "") . "category IN(" . $wherecatin . ")";
        }

        if ($where != "") {
            $where = "WHERE $where";
        }

        if ($_GET["sort"] || $_GET["order"]) {
            switch ($_GET["sort"]) {
                case 'name':$sort = "torrents.name";
                    $addparam .= "sort=name&amp;";
                    break;
                case 'times_completed':$sort = "torrents.times_completed";
                    $addparam .= "sort=times_completed&amp;";
                    break;
                case 'seeders':$sort = "torrents.seeders";
                    $addparam .= "sort=seeders&amp;";
                    break;
                case 'leechers':$sort = "torrents.leechers";
                    $addparam .= "sort=leechers&amp;";
                    break;
                case 'comments':$sort = "torrents.comments";
                    $addparam .= "sort=comments&amp;";
                    break;
                case 'size':$sort = "torrents.size";
                    $addparam .= "sort=size&amp;";
                    break;
                default:$sort = "torrents.id";
            }

            if ($_GET["order"] == "asc" || ($_GET["sort"] != "id" && !$_GET["order"])) {
                $sort .= " ASC";
                $addparam .= "order=asc&amp;";
            } else {
                $sort .= " DESC";
                $addparam .= "order=desc&amp;";
            }

            $orderby = "ORDER BY $sort";

        } else {
            $orderby = "ORDER BY torrents.sticky ASC, torrents.id DESC";
            $_GET["sort"] = "id";
            $_GET["order"] = "desc";
        }

        //Get Total For Pager
        $count = Torrents::getCatwhere($where);

        //get sql info
        if ($count) {
            list($pagertop, $pagerbottom, $limit) = pager(20, $count, "search/browse?" . $addparam);
            $query = "SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.sticky, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.tube, torrents.imdb, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $orderby $limit";
            $res = DB::run($query);
        } else {
            unset($res);
        }

        Style::header(Lang::T("BROWSE_TORRENTS"));
        Style::begin(Lang::T("BROWSE_TORRENTS"));

        // get all parent cats
        echo "<center><b>" . Lang::T("CATEGORIES") . ":</b> ";
        $catsquery = Torrents::getCatByParent();
        echo " - <a href=" . URLROOT . "/search/browse'>" . Lang::T("SHOW_ALL") . "</a>";
        while ($catsrow = $catsquery->fetch(PDO::FETCH_ASSOC)) {
            echo " - <a href=" . URLROOT . "/search/browse/?parent_cat=" . urlencode($catsrow['parent_cat']) . "'>$catsrow[parent_cat]</a>";
        }
        ?>
                <br /><br />
                <form method="get" action="<?php echo URLROOT ?>/search/browse">
                <table align="center">
                <tr align='right'>
                <?php
        $i = 0;
        $cats = Torrents::getCatByParentName();
        while ($cat = $cats->fetch(PDO::FETCH_ASSOC)) {
            $catsperrow = 5;
            print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
            print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a href=" . URLROOT . "/search/browse?cat={$cat["id"]}'>" . htmlspecialchars($cat["parent_cat"]) . " - " . htmlspecialchars($cat["name"]) . "</a> <input name='c{$cat["id"]}' type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) || $_GET["cat"] == $cat["id"] ? "checked='checked' " : "") . "value='1' /></td>\n");
            $i++;
        }
        echo "</tr><tr align='center'><td colspan='$catsperrow' align='center'><input type='submit' value='" . Lang::T("GO") . "' /></td></tr>";
        echo "</table></form>";

        //if we are browsing, display all subcats that are in same cat
        if ($parent_cat) {
            $thisurl .= "parent_cat=" . urlencode($parent_cat) . "&amp;";
            echo "<br /><br /><b>" . Lang::T("YOU_ARE_IN") . ":</b> <a href='" . URLROOT . "/search/browse?parent_cat=" . urlencode($parent_cat) . "'>" . htmlspecialchars($parent_cat) . "</a><br /><b>" . Lang::T("SUB_CATS") . ":</b> ";
            $subcatsquery = DB::run("SELECT id, name, parent_cat FROM categories WHERE parent_cat=" . sqlesc($parent_cat) . " ORDER BY name");
            while ($subcatsrow = $subcatsquery->fetch(PDO::FETCH_ASSOC)) {
                $name = $subcatsrow['name'];
                echo " - <a href=" . URLROOT . "/search/browse?cat=$subcatsrow[id]'>$name</a>";
            }
        }

        if (Validate::Id($_GET["page"])) {
            $thisurl .= "page=$_GET[page]&amp;";
        }

        echo "</center><br /><br />"; //some spacing
        // New code (TorrentialStorm)
        echo "<div align='right'><form id='sort' action=''>" . Lang::T("SORT_BY") . ": <select name='sort' onchange='window.location=\"{$thisurl}sort=\"+this.options[this.selectedIndex].value+\"&amp;order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value'>";
        echo "<option value='id'" . ($_GET["sort"] == "id" ? " selected='selected'" : "") . ">" . Lang::T("ADDED") . "</option>";
        echo "<option value='name'" . ($_GET["sort"] == "name" ? " selected='selected'" : "") . ">" . Lang::T("NAME") . "</option>";
        echo "<option value='comments'" . ($_GET["sort"] == "comments" ? " selected='selected'" : "") . ">" . Lang::T("COMMENTS") . "</option>";
        echo "<option value='size'" . ($_GET["sort"] == "size" ? " selected='selected'" : "") . ">" . Lang::T("SIZE") . "</option>";
        echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? " selected='selected'" : "") . ">" . Lang::T("COMPLETED") . "</option>";
        echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? " selected='selected'" : "") . ">" . Lang::T("SEEDERS") . "</option>";
        echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? " selected='selected'" : "") . ">" . Lang::T("LEECHERS") . "</option>";
        echo "</select>&nbsp;";
        echo "<select name='order' onchange='window.location=\"{$thisurl}order=\"+this.options[this.selectedIndex].value+\"&amp;sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value'>";
        echo "<option selected='selected' value='asc'" . ($_GET["order"] == "asc" ? " selected='selected'" : "") . ">" . Lang::T("ASCEND") . "</option>";
        echo "<option value='desc'" . ($_GET["order"] == "desc" ? " selected='selected'" : "") . ">" . Lang::T("DESCEND") . "</option>";
        echo "</select>";
        echo "</form></div>";
        // End

        if ($count) {
            torrenttable($res);
            print($pagerbottom);
        } else {
            print(Lang::T("NOTHING_FOUND") . "&nbsp;&nbsp;");
            print Lang::T("NO_UPLOADS");
        }
        if ($_SESSION) {
            DB::run("UPDATE users SET last_browse=? WHERE id=?", [TimeDate::gmtime(), $_SESSION['id']]);
        }
        Style::end();
        Style::footer();
    }

    public function needseed()
    {
        if ($_SESSION["view_torrents"] == "no") {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_VIEW"));
        }
        $res = DB::run("SELECT torrents.id, torrents.name, torrents.owner, torrents.external, torrents.size, torrents.seeders, torrents.leechers, torrents.times_completed, torrents.added, users.username FROM torrents LEFT JOIN users ON torrents.owner = users.id WHERE torrents.banned = 'no' AND torrents.leechers > 0 AND torrents.seeders <= 1 ORDER BY torrents.seeders");
        if ($res->rowCount() == 0) {
            Redirect::autolink(URLROOT, Lang::T("NO_TORRENT_NEED_SEED"));
        }
        $title = Lang::T("TORRENT_NEED_SEED");
        $data = [
            'title' => $title,
            'res' => $res,
        ];
        View::render('torrent/needseed', $data, 'user');
    }

}