<?php
require 'functions.php';
$card = get_card($_GET['card_sha']);
if (empty($card)) {
	http_response_code(404);
	?>
	The card cannot be found.
	<?php
}
else {
?>
<html>
<head>
<meta charset="UTF-8">
<title>Julekort fra <?php print $card['sender_name']; ?></title>
</head>
<body>
<article>
	<div>
		<span>Fra:</span><?php print $card['sender_name']; ?> <?php print maillink($card['sender_email']); ?>
		<span>Til:</span><?php print $card['recipient_name']; ?> <?php print maillink($card['recipient_email']); ?>
	</div>
	<?php print message_filter($card['message']); ?>
</article>
</body>
</html>
<?
}