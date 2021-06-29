</div>
<!-- END MIDDLE COLUMN -->
<!-- START RIGHT COLUMN -->
    <?php if (RIGHTNAV){ ?>
<div class="col-sm-2">
    <?php Block::right(); ?>
</div>
    <?php } ?>
<!-- END RIGHT COLUMN -->
</div>
</div>
<!-- END MAIN -->

<!-- Footer -->
<footer>
  <table class="card-footer">
  <tbody>
    <tr>
      <th scope="col">© 2005-2020 - Not Them Limited</th>
      <th scope="col">Our Friends</th>
      <th scope="col">Feedback</th>
      <th scope="col">Social Media</th>
    </tr>
    <tr>
      <td>Disclaimer:None of the files shown here are actually hosted on this server. The links are provided solely by the site's users. The Administrators & Moderators of this site cannot be held responsible for what its users
	  post, or any other actions of its users. <br>You may not use this site to distribute or download any official released commercial material for which you do not have the legal rights to do so. It is your own responsibility to adhere to these terms. <br>Any copyrighted content that is reported to the Administrators will be removed immediately, in accordance with the DMCA.<br><br>U2Torrents.com is not affiliated with U2, LiveNation, Universal Island Records, InterScope Records or Maverick Management.<br></td>
      <td><a href="http://www.u2radio.com/" target="_blank">U2 ZOO Station Radio</a><br><a href="http://www.u2gigs.com/" target="_blank">U2 Gigs</a><br><a href="http://thetradersden.org/" target="_blank">TheTradersDen</a></td>
      <td><a href="https://bootstrap.u2sites.xyz/contactstaff" target="_parent">Contact MODS</a><br><a href="https://bootstrap.u2sites.xyz/forums?action=forumview&forid=1" target="_parent">HELP Forum</a><br><a href="https://bootstrap.u2sites.xyz/linkback" target="_parent">Share U2Torrents.com!</a></td>
      <td><a href="https://twitter.com/u2torrents" target="_blank"><img src="../assets/images/followus_twitter.png" width="145" height="24" alt=""/></a><br><br>
        <a href="https://www.facebook.com/pages/U2t/117634184954578" target="_blank"><img src="../assets/images/followus_facebook.png" width="145" height="24" alt=""/></a></td>
    </tr>
      <tr>
        <td><br><?php printf(Lang::T("POWERED_BY_TT"), VERSION);?> // <?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart'];?><?php printf(Lang::T("PAGE_GENERATED_IN"), $totaltime);?> // <a href="https://torrenttrader.uk" target="_blank">torrenttrader.uk</a> -|- <a href='<?php echo URLROOT; ?>/rss'><i class="fa fa-rss-square"></i> <?php echo Lang::T("RSS_FEED"); ?></a> - <a href='<?php echo URLROOT; ?>/rss/custom'><?php echo Lang::T("FEED_INFO"); ?></a> // Themed by Propaganda // Coded By: M-jay ©2021</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </tbody>
  </table>
</footer>
<!-- Footer -->
    <!-- Dont Change -->
    <script src="<?php echo URLROOT; ?>/assets/js/jquery-3.3.1.min.js"></script>
    <script src="<?php echo URLROOT; ?>/assets/js/popper.js"></script>
    <script src="<?php echo URLROOT; ?>/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo URLROOT; ?>/assets/js/java_klappe.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
    <script>
        function updateShouts(){
            // Assuming we have #shoutbox
            $('#shoutbox').load('shoutbox/chat');
        }
        setInterval( "updateShouts()", 15000 );
        updateShouts();
    </script>
<script>
function myFunction() {
  var x = document.getElementById("myDIVsmileytog");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>
<script src="<?php echo URLROOT; ?>/sceditor/minified/sceditor.min.js"></script>
		<script src="<?php echo URLROOT; ?>/sceditor/minified/icons/monocons.js"></script>
		<script src="<?php echo URLROOT; ?>/sceditor/minified/formats/bbcode.js"></script>
    <script src="<?php echo URLROOT; ?>/assets/js/sceditor.js"></script>
  </body>
</html>
<?php ob_end_flush(); ?>