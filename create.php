<?php
require "functions.php";
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['op'] != $strings['edit_button']) {
	// validate form input
	$errors = create_form_validate($_POST);
	if (!$errors) {
		if ($_POST['op'] == $strings['send_button']) {
			// create card
			$sha1 = create_card($_POST);
			// Send mail
			$card = get_card($sha1);
			$mail = generate_card_mail($card);
			$status = send_mail($mail);
			if ($status === TRUE) {
				// Set success message
				set_session_status($strings['send_card_confirm_message'], 'success');
			}
			else {
				set_session_status(json_encode($status), 'error');
			}
			// Forward to card
			header("Location: card.php?card_sha=$sha1&register=no");
		}
		elseif ($_POST['op'] == $strings['preview_button']) {
			$card = $_POST;
			$card_content = card_render($card);
			$preview_view = new Template();
			$preview_view->send_button = $strings['send_button'];
			$preview_view->edit_button = $strings['edit_button'];
			$preview = $preview_view->render('templates/preview.tpl.php');
			$page = new Template();
			$page->content = $card_content;
			$page->body_classes = 'card loading';
			$page->top_menu = $preview;
			$page->status = $strings['preview_status_message'];
			$page->status_class = 'info';
			$page->title = strtr($strings['page_title'], array(':sender_name' => $card['sender_name']));
			print $page->render('templates/page.tpl.php');
		}

	}
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' || $errors || (isset($_POST) && $_POST['op'] == $strings['edit_button'])) { 
	$form = new Template();
	$form->errors = $errors;
	$form->send_button = $strings['send_button'];
	$form->preview_button = $strings['preview_button'];
	$form_content = $form->render('templates/createcard.tpl.php');
	$page = new Template();
	if (!empty($errors)) {
		$page->status = '';
		$page->status_class = 'error';
		foreach($errors as $error) {
			$page->status .= $error . '<br />';
		}
	}
	$page->content = $form_content;
	$page->title = $strings['page_title_create'];
	print $page->render('templates/page.tpl.php');
}
