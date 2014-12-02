<?php
require "functions.php";
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// validate form input
	$errors = create_form_validate($_POST);
	if (!$errors) {
		// create card
		$sha1 = create_card($_POST);
		// TODO set success message
		// Send mail
		$card = get_card($sha1);
		$mail = generate_card_mail($card);
		$status = send_mail($mail);
		var_export($status);
		if ($status === TRUE) {
			// Set sent success message
			print 'oK';
		}
		else {
			// Set error message to $json_encode(status);
			print json_encode(status);
		}
		// Forward to card
		header("Location: card.php?card_sha=$sha1");
	}
	?>
	bla bla bla <?php
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' || $errors) { 
	if (!empty($errors)) {
		var_dump($errors);
	}
	?>
<html>
<head>
<meta charset="UTF-8">
</head>
<form method="post" action="<?php print $_SERVER['REQUEST_URI'];?>">
To email: <input name="recipient_email" <?php print errorclass($errors, 'recipient_email')?> <?php print currentvalue('recipient_email')?>/><br />
To name: <input name="recipient_name" <?php print errorclass($errors, 'recipient_name')?> <?php print currentvalue('recipient_name')?>/><br />
Your name: <input name="sender_name" <?php print errorclass($errors, 'sender_name')?> <?php print currentvalue('sender_name')?>/><br />
Your email: <input name="sender_email" <?php print errorclass($errors, 'sender_email')?> <?php print currentvalue('sender_email')?>/><br />
Message
<textarea name="message" <?php print errorclass($errors, 'message')?>><?php print currentvalue('message', FALSE)?></textarea>
<input type="submit"/>
</form>
</html>
<?php } ?>
