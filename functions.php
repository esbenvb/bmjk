<?php
setup();
function create_form_validate($data) {
	require "libraries/rfc822.php";
	global $strings;
	$errors = array();
	if (empty($data['recipient_name'])) {
		$errors['recipient_name'] = $strings['input_error_recipient_name'];
	}
	if (empty($data['sender_name'])) {
		$errors['sender_name'] = $strings['input_error_sender_name'];
	}

	if (!is_valid_email_address($data['recipient_email'])) {
		$errors['recipient_email'] = $strings['input_error_recipient_email'];
	}
	if (!is_valid_email_address($data['sender_email'])) {
		$errors['sender_email'] = $strings['input_error_sender_email'];
	}

	if (empty($data['message'])) {
		$errors['message'] = $strings['input_error_message'];
	}
	return $errors;
}

function errorclass($errors, $key) {
	if (isset($errors[$key])) {
		return 'error';
	}
	return '';
}

function currentvalue($key, $attribute = TRUE) {
	if (!empty($_POST[$key])) {
		if ($attribute) {
			return 'value = "' . htmlspecialchars($_POST[$key]) . '"';
		}
		else {
			return htmlspecialchars($_POST[$key]);
		}
	}
	return '';
}

function setup() {
	global $config, $strings, $db;
	require 'settings.php';
	require 'strings.php';
	require 'libraries/Template.class.php';
	$db = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8', $config['db_user'], $config['db_pass']);
	session_start();
}

function db() {
	global $db;
	return $db;
}

function create_card($data) {
	$values = array(
		':sha1' => sha1($data['sender_email'] . $data['recipient_email'] . $data['message'] . microtime()),
		':message' => $data['message'],
		':recipient_email' => $data['recipient_email'],
		':recipient_name' => $data['recipient_name'],
		':sender_email' => $data['sender_email'],
		':sender_name' => $data['sender_name'],
		':sender_ip' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
	);
	$stmt = db()->prepare('INSERT INTO card (sha1, message, recipient_email, recipient_name, sender_email, sender_name, sender_ip, created) VALUES (:sha1, :message, :recipient_email, :recipient_name, :sender_email, :sender_name, :sender_ip, unix_timestamp())');
	$stmt->execute($values);
	if ($stmt->rowCount() == 1) {
		return $values[':sha1'];	
	}
}

function get_card($sha1) {
	$stmt = db()->prepare('SELECT * FROM card WHERE sha1 = :sha1');
	$stmt->execute(array(':sha1' => $sha1));
	$row = $stmt->fetch();
	return $row;
}

function message_filter($message) {
	$message = htmlentities($message);
	$message = nl2br($message);
	return $message;
}

function maillink($email, $text = NULL, $attributes = array()) {
	$text = isset($text) ? $text : $email;
	return makelink('mailto:' . $email, $text, $attributes);
}

function makelink($href, $text = NULL, $attributes = array()) {
	$text = isset($text) ? $text : $email;
	$attributes_str = '';
	foreach ($attributes as $key => $val) {
		$attributes_str .= ' ' . $key . '="' . $val . '"';
	}
	return '<a href="' . $href . '" ' . $attributes_str . '>' . $text .  '</a>';	
}



function send_mail($mail) {
	global $config;
	$url = 'https://api.sendgrid.com/';
	$user = $config['sendgrid_user'];
	$pass = $config['sendgrid_password'];
	$params = array(
	    'api_user'  => $user,
	    'api_key'   => $pass,
	    'to'        => $mail['to'],
	    'subject'   => $mail['subject'],
	    'html'      => isset($mail['html']) ? $mail['html'] : NULL,
	    'text'      => $mail['text'],
	    'from'      => $mail['from'],
	    'fromname'  => $mail['fromname'],
	  );
	$request =  $url.'api/mail.send.json';
	// Generate curl request
	$session = curl_init($request);
	// Tell curl to use HTTP POST
	curl_setopt ($session, CURLOPT_POST, true);
	// Tell curl that this is the body of the POST
	curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
	// Tell curl not to return headers, but do return the response
	curl_setopt($session, CURLOPT_HEADER, false);
	// Tell PHP not to use SSLv3 (instead opting for TLS)
	//curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
	curl_setopt($session, CURLOPT_SSLVERSION, 6);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

	// obtain response
	$response_json = curl_exec($session);
	curl_close($session);

	// print everything out
	$response = json_decode($response_json);
	if ($response->message == 'success') {
		return TRUE;
	}
	else {
		return $response->errors;
	}
}

function generate_card_mail($card) {
	global $config, $strings;
	$mail = array();
	$mail['to'] = $card['recipient_email'];
	$mail['toname'] = $card['recipient_name'];
	$mail['subject'] = strtr($strings['mail_subject'], array(':sender_name' => $card['sender_name']));
	$mail['text'] = julekort_mail_text($card);
	$mail['html'] = julekort_mail_html($card);
 	$mail['from'] = $config['mail_sender_email'];
	$mail['fromname'] = $config['mail_sender_name'];
	return $mail;
}

function julekort_mail_html($card) {
	global $config;
	$mail = new Template();
	$mail->card_url = $config['base_url'] . '/card.php?card_sha=' . $card['sha1'];
	$mail->image_url = $config['base_url'] . '/images/mail.jpg';
	$mail->logo_url = $config['base_url'] . '/images/logo-mail.png';
	foreach ($card as $key => $value) {
			$mail->{$key} = $value;
	}
	return $mail->render('templates/mail_html.tpl.php');
}

function julekort_mail_text($card) {
	global $config;
	$mail = new Template();
	$mail->card_url = $config['base_url'] . '/card.php?card_sha=' . $card['sha1'];
	return $mail->render('templates/mail_text.tpl.php');
}

function set_session_status($message, $class) {
	$_SESSION['status_message'] = $message;
	$_SESSION['status_message_class'] = $class;
}

function show_session_status($view) {
	if (isset($_SESSION['status_message'])) {
		$view->status = $_SESSION['status_message'];
		$view->status_class = isset($_SESSION['status_message_class']) ? $_SESSION['status_message_class'] : '';
		unset($_SESSION['status_message_class']);
		unset($_SESSION['status_message']);
	}
}

function card_render($card) {
	$card_view = new Template();
	foreach ($card as $key => $value) {
		$card_view->{$key} = $value;
	}
	$card_view->message_filtered = message_filter($card_view->message);
	$card_view->sender_email_link = maillink($card_view->sender_email);
	$card_view->recipient_email_link = maillink($card_view->recipient_email);
	$card_content = $card_view->render('templates/card.tpl.php');
	return $card_content;
}

function register_read($card) {
	if ((isset($_GET['register']) && $_GET['register'] == 'no') || isset($card['opened'])) {
		return FALSE;
	}
	$stmt = db()->prepare('UPDATE card SET opened=unix_timestamp(), opened_ip=:opened_ip WHERE sha1 = :sha1');
	$stmt->execute(array(
		':opened_ip' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
		':sha1' => $card['sha1'],
	));
	if ($stmt->rowCount() == 1) {
		return TRUE;
	}
}

function notification_mail($card) {
	global $config, $strings;
	$mail = new Template();
	$mail->card_url = $config['base_url'] . '/card.php?card_sha=' . $card['sha1'];
	$mail->read_time = $read_time;
	foreach ($card as $key => $value) {
		$mail->{$key} = $value;
	}
	$mail->opened_formatted = date($strings['card_read_notification_time_format'], $mail->opened);
	return $mail->render('templates/card_read_notification_mail.tpl.php');
}

function generate_notification_mail($card) {
	global $config, $strings;
	$mail = array();
	$mail['to'] = $card['sender_email'];
	$mail['toname'] = $card['sender_name'];
	$mail['subject'] = strtr($strings['card_read_notification_subject'], array(':recipient_name' => $card['recipient_name']));
	$mail['text'] = notification_mail($card);
	$mail['from'] = $config['mail_sender_email'];
	$mail['fromname'] = $config['mail_sender_name'];
	return $mail;
}

function card_register_ok($sha1, $flag) {
	$stmt = db()->prepare('UPDATE card SET ' . $flag . ' = 1 WHERE sha1 = :sha1');
	$stmt->execute(array(
		':sha1' => $sha1,
	));
	$stmt->debugDumpParams();
	return $stmt->rowCount();
}
