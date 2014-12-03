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
		if ($status === TRUE) {
			// Set sent success message
		}
		else {
			// Set error message to $json_encode(status);
			print json_encode($status);
		}
		// Forward to card
		header("Location: card.php?card_sha=$sha1");
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' || $errors) { 
	if (!empty($errors)) {
		var_dump($errors);
	}
	$form = new Template();
	$form_content = $form->render('templates/createcard.tpl.php');
	$page = new Template();
	$page->content = $form_content;
	$page->title = 'Opret julekort';
	print $page->render('templates/page.tpl.php');
}
