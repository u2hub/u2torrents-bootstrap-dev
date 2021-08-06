<?php
usermenu($data['userid']);
if ($data['friend']->rowCount() == 0) { ?>&nbsp;&nbsp;
    <div class="row ttborder"> <center><b>Friend list is empty!</b></center></div>
    <?php
} else { ?>
    <center><b>Friend list</b></center> <?php
    while ($friend = $data['friend']->fetch(PDO::FETCH_ASSOC)) {
        $avatar = htmlspecialchars($friend["avatar"]);
        if (!$avatar) {
            $avatar = "".URLROOT."/assets/images/default_avatar.png";
        }
        ?>
        <div class="row ttborder"> 
        <div class="col-md-4 mt-3">
        <img width=80px src="<?php echo $avatar ?>">&nbsp;<a href='<?php echo URLROOT ?>/profile?id=<?php echo $friend['id'] ?>'><b><?php echo  Users::coloredname($friend['name']) ?></b></a> &nbsp;
        <a href='<?php echo  URLROOT ?>/messages/create?id=<?php echo $friend['id'] ?>'><img src='<?php echo  URLROOT ?>/assets/images/button_pm.gif' title=Send&nbsp;PM border=0></a>&nbsp;
        <a href='<?php echo  URLROOT ?>/friends/delete?id=<?php echo $data['userid'] ?>&type=friend&targetid=<?php echo  $friend['id'] ?>'><img src='<?php echo URLROOT ?>/assets/images/delete.png' title=Remove border=0></a>
        <div style='margin-top:10px; margin-bottom:2px'>Last seen: <?php echo  date("<\\b>d.M.Y<\\/\\b> H:i", TimeDate::utc_to_tz_time($friend['last_access'])) ?></div>
        [<b><?php echo  TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($friend['last_access'])) ?> ago</b>]
        </div>
        </div>
        <?php
    }
}
?>
<br>
 
<?php
if ($data['enemy']->rowCount() == 0) { ?>&nbsp;&nbsp;
    <div class="row ttborder"> <center><b>Unfriended list is empty!</b></center></div>
    <?php
} else { ?>
    <center><b>Unfriended list</b></center> <?php
    while ($enemy = $data['enemy']->fetch(PDO::FETCH_ASSOC)) {
        $avatar = htmlspecialchars($enemy["avatar"]);
        if (!$avatar) {
            $avatar = "".URLROOT."/assets/images/default_avatar.png";
        }
        ?>
        <div class="row ttborder"> 
        <div class="col-md-4">
        <img width=80px src="<?php echo $avatar ?>">&nbsp;<a href='<?php echo  URLROOT ?>/profile?id=<?php echo  $enemy['id'] ?>'><b><?php echo  Users::coloredname($enemy['name']) ?></b></a> &nbsp;
        <a href='<?php echo URLROOT ?>/messages/create?id=<?php echo $enemy['id'] ?>'><img src='<?php echo  URLROOT ?>/assets/images/button_pm.gif' title=Send&nbsp;PM border=0></a>&nbsp;
        <a href='<?php echo URLROOT ?>/friends/delete?id=<?php echo $data['userid']?>&type=friend&targetid=<?php echo  $enemy['id'] ?>'><img src='<?php echo  URLROOT ?>/assets/images/delete.png' title=Remove border=0></a>
        <div style='margin-top:10px; margin-bottom:2px'>Last seen: <?php echo  date("<\\b>d.M.Y<\\/\\b> H:i", TimeDate::utc_to_tz_time($enemy['last_access'])) ?></div>
        [<b><?php echo TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($enemy['last_access'])) ?> ago</b>]
        </div>
        </div>
        <?php
    }
}