<?php
Style::begin("Invited Users");
    ?>
    <center>
    This page displays all invited users which have been sent invites and have activated there account. By deleting users the inviter will recieve there invite back and any data associated with the invitee will be deleted. <?php echo number_format($data['count']); ?> members have confirmed invites;
    </center>
    <?php if ($data['count'] > 0): ?>
    <br />
     <form id="invited" method="post" action="<?php echo URLROOT; ?>/admininviteusers?do=del">
    <input type="hidden" name="do" value="del" />
    <div class='table-responsive'><table class='table table-striped'>
    <thead>
    <tr>
        <th>Username</th>
        <th>E-mail</th>
        <th><?php echo Lang::T("CLASS"); ?></th>
        <th>Invited</th>
        <th>Last Access</th>
        <th>Invited By</th>
        <th><input type="checkbox" name="checkall" onclick="checkAll(this.form.id);" /></th>
    </tr></thead>
    <?php while ($row = $data['res']->fetch(PDO::FETCH_ASSOC)): ?>
    <tbody><tr>
        <td><a href="<?php echo URLROOT; ?>/users/profile?id=<?php echo $row["id"]; ?>"><?php echo Users::coloredname($row["username"]); ?></a></td>
        <td><?php echo $row["email"]; ?></td>
        <td><?php echo get_user_class_name($row["class"]); ?></td>
        <td><?php echo TimeDate::utc_to_tz($row["added"]); ?></td>
        <td><?php echo TimeDate::utc_to_tz($row["last_access"]); ?></td>
        <td><?php echo ($row['inviter']) ? '<a href="' . URLROOT . '/users/profile?id=' . $row["invited_by"] . '">' . $row["inviter"] . '</a>' : 'Unknown User'; ?></td>
        <td><input type="checkbox" name="users[]" value="<?php echo $row["id"]; ?>" /></td>
    </tr>
    <?php endwhile;?>
    <tr>
        <td>
        <button type='submit' class='btn btn-xs btn-danger'>Delete Checked</button>
        </td>
    </tr>
    <tbody></table></div>
    </form>
    <?php
endif;
    if ($data['count'] > 25) {
        echo $data['pagerbottom'];
    }
    Style::end();