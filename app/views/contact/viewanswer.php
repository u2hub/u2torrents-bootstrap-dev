<?php
Style::begin("Viewing Answer");
?>
<table class='table table-striped table-bordered table-hover'><thead><tr><td>
<?php $elapsed = TimeDate::get_elapsed_time(TimeDate::sql_timestamp_to_unix_timestamp($arr4["added"])); ?>

<b><a href="<?php echo URLROOT; ?>/profile?id=<?php echo $data['answeredby']; ?>"><?php echo Users::coloredname($arr5['username']); ?></a>
</b> answered this message sent by <?php echo $data['sender']; ?>
<br><br style='margin-bottom: -10px'><div align=left><b>Subject: <?php echo $data['subject']; ?></b>
<br><b>Answer:</b>
<?php echo format_comment($data['answer']); ?>
</td></tr></table>
<?php
Style::end();