<?php
require 'functions.php';
$card = get_card($_GET['card_sha']);
if (empty($card)) {
	http_response_code(404);
	$page = new Template();
	$page->content = '';
	$page->status = $strings['card_not_found_status_message'] ;
	$page->status_class = 'error';
	$page->title = $strings['page_title_card_not_found'] ;
	print $page->render('templates/page.tpl.php');
}
else {
	$card_content = card_render($card);
	$page = new Template();
	show_session_status($page);
	$page->content = $card_content;
	if (!(isset($_GET['register']) && $_GET['register'] == 'no') && !isset($card['opened'])) {
		$register_code = new Template();
		$register_code->sha1 = $card['sha1'];
		$page->footer = $register_code->render('templates/register_read.tpl.php');
	}
	$page->body_classes = 'card loading';
	$page->title = strtr($strings['page_title'], array(':sender_name' => $card['sender_name']));
	print $page->render('templates/page.tpl.php');
}
