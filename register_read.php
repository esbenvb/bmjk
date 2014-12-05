<?php
require 'functions.php';
$card = get_card($_GET['card_sha']);
if (empty($card)) {
	http_response_code(404);
	print -1;
}
else {
	$read_status = register_read($card);
	if ($read_status) {
		// Reload card to get opened timestamp.
		$card = get_card($card['sha1']);
		$mail = generate_notification_mail($card);
		send_mail($mail);
		print 1;
	}
	else {
		print 0;
	}
}
