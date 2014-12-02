<div class="col-md-4 col-md-push-8 text-center tree">
      <img src="images/tree.png" class="img-responsive">
</div>
<div class="col-md-8 col-md-pull-4 relative card-content">
	<div class="card-meta">
		<p><strong>Til:</strong> <?php print $sender_name; ?> (<?php print $sender_email_link; ?>)</p>
		<p><strong>Fra:</strong> <?php print $recipient_name; ?> (<?php print $recipient_email_link; ?>)</p>
	</div>
		<p><?php print $recipient_name; ?>,</p>
		<?php print $message; ?>
</div>