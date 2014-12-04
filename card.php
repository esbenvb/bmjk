<?php
require 'functions.php';
global $config;
$card = get_card($_GET['card_sha']);
if (empty($card)) {
	http_response_code(404);
	$page = new Template();
	$page->content = '';
	$page->status = 'The card cannot be found.';
	$page->status_class = 'error';
	$page->title = 'NOT FOUND';
	print $page->render('templates/page.tpl.php');
}
else {
	$card_view = new Template();
	foreach ($card as $key => $value) {
		$card_view->{$key} = $value;
	}
	$card_view->message_filtered = message_filter($card_view->message);
	$card_view->sender_email_link = maillink($card_view->sender_email);
	$card_view->recipient_email_link = maillink($card_view->recipient_email);
	$card_content = $card_view->render('templates/card.tpl.php');
	$page = new Template();
	show_session_status($page);
	$page->content = $card_content;
	$page->body_classes = 'card loading';
	$page->title = strtr($config['page_title'], array(':sender_name' => $card['sender_name']));
	print $page->render('templates/page.tpl.php');
}
