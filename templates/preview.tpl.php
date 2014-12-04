<div class="top-menu">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
   	    <form role="form" method="post" action="<?php print $_SERVER['REQUEST_URI'];?>">
			<input type="hidden" name="sender_name" <?php print currentvalue('sender_name')?>>
			<input type="hidden" name="sender_email" <?php print currentvalue('sender_email')?>>
			<input type="hidden" name="recipient_name" <?php print currentvalue('recipient_name')?>>
			<input type="hidden" name="recipient_email" <?php print currentvalue('recipient_email')?>>
			<input type="hidden" name="message" <?php print currentvalue('message')?>>
			<input type="hidden"><!--TODO: Send mig en mail når kortet er blevet læst-->
			<input type="submit" name="op" class="btn btn-default btn-xs" value="<?php print $edit_button; ?>" />
			<input type="submit" name="op" class="btn btn-success btn-xs pull-right" value="<?php print $send_button; ?>" />
		</form>
      </div>
    </div>
  </div>
</div>
