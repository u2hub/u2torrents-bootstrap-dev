<br><?php
if ($_SESSION['class'] > _MODERATOR) { ?>
<div class="border ttborder">
    <center><font color=#ff9900>Super Moderator Only</font></center>
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admingroups/groups"><img src="<?php echo URLROOT; ?>/assets/images/admin/user_groups.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("USER_GROUPS_VIEW"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminshoutbox/clear"><img src="<?php echo URLROOT; ?>/assets/images/shoutclear.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("CLEAR_SHOUTBOX"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admincontactstaff/staffbox"><img src="<?php echo URLROOT; ?>/assets/images/admin/staffmess.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Staff Messages</b></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminlog"><img src="<?php echo URLROOT; ?>/assets/images/admin/site_log.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("SITELOG"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/admincensor"><img src="<?php echo URLROOT; ?>/assets/images/admin/word_censor.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("WORD_CENSOR"); ?></b></a></li>
    </ul>
</div> <?php
} ?>
<br>
<div class="border ttborder">
    <center><font color=#ff9900>Invites</font></center>
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/Admininvites/pending"><img src="<?php echo URLROOT; ?>/assets/images/admin/pending_invited_user.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Pending Invited Users</b></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/Admininvites"><img src="<?php echo URLROOT; ?>/assets/images/admin/invited_user.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Invited Users</b></a></li>   
    </ul>
</div>
<br>
<div class="border ttborder">
    <center><font color=#ff9900>Warning/Reports</font></center>
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminwarning"><img src="<?php echo URLROOT; ?>/assets/images/admin/warned_user.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("WARNED_USERS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminreports"><img src="<?php echo URLROOT; ?>/assets/images/admin/report_system.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("REPORTS"); ?></b></a></li>
    </ul>
</div>
<br>
<div class="border ttborder">
    <center><font color=#ff9900>Bans</font></center>
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminbans/ip"><img src="<?php echo URLROOT; ?>/assets/images/admin/ip_block.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("BANNED_IPS"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminclient"><img src="<?php echo URLROOT; ?>/assets/images/admin/client.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Client Ban</b></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/Adminusers/cheats"><img src="<?php echo URLROOT; ?>/assets/images/admin/cheats.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("Detect Cheats"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/Adminusers/duplicateip"><img src="<?php echo URLROOT; ?>/assets/images/admin/double-ip.ico" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("DUPLICATEIP"); ?></b></a></li> 
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminbans/email"><img src="<?php echo URLROOT; ?>/assets/images/admin/mail_bans.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("EMAIL_BANS"); ?></b></a></li>
    </ul>
</div>
<br>
<div class="border ttborder">
    <center><font color=#ff9900>Users</font></center>
    <ul class="list-group">
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminusers/add"><img src="<?php echo URLROOT; ?>/assets/images/admin/adduser.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("Add User"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminsearch/advancedsearch"><img src="<?php echo URLROOT; ?>/assets/images/admin/user_search.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("ADVANCED_USER_SEARCH"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminavatar"><img src="<?php echo URLROOT; ?>/assets/images/admin/avatar_log.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("AVATAR_LOG"); ?></b></a></li>
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminusers/privacy"><img src="<?php echo URLROOT; ?>/assets/images/admin/privacy_level.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Privacy Level</b></a></li>    
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminsearch/simplesearch"><img src="<?php echo URLROOT; ?>/assets/images/admin/simple_user_search.png" border="0" width="20" height="20" alt="" />&nbsp;<b>Simple User Search</b></a></li> 
    <li class="list-group-item"><a href="<?php echo URLROOT; ?>/adminusers/whoswhere"><img src="<?php echo URLROOT; ?>/assets/images/admin/whos_where.png" border="0" width="20" height="20" alt="" />&nbsp;<b><?php echo Lang::T("WHOS_WHERE"); ?></b></a></li>
    </ul>
</div>