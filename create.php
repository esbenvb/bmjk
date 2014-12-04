<?php
require "functions.php";
$errors = array();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// validate form input
	$errors = create_form_validate($_POST);
	if (!$errors) {
		// create card
		$sha1 = create_card($_POST);
		// Send mail
		$card = get_card($sha1);
		$mail = generate_card_mail($card);
		$status = send_mail($mail);
		if ($status === TRUE) {
			// Set success message
			set_session_status('Dit kort er blevet sendt.<br /> <a href="create.php" class="btn btn-default">Send et nyt kort</a>', 'success');
		}
		else {
			set_session_status(json_encode($status), 'error');
		}
		// Forward to card
		header("Location: card.php?card_sha=$sha1");
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' || $errors) { 
	$form = new Template();
	$form->errors = $errors;
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
	$page->title = 'Opret julekort';
	print $page->render('templates/page.tpl.php');
}
