<?php
setup();
function create_form_validate($data) {
	require "libraries/rfc822.php";
	$errors = array();
	if (empty($data['recipient_name'])) {
		$errors['recipient_name'] = 'Du mangler at udfylde modtagerens navn.';
	}
	if (empty($data['sender_name'])) {
		$errors['sender_name'] = 'Du mangler at udfylde afsenderens navn.';
	}
	
	if (!is_valid_email_address($data['recipient_email'])) {
		$errors['recipient_email'] = 'Indtast en gyldig modtager-mailadresse.';
	}
	if (!is_valid_email_address($data['sender_email'])) {
		$errors['sender_email'] = 'Indtast en gyldig afsender-mailadresse.';
	}
	if (empty($data['message'])) {
		$errors['message'] = 'Du mangler at udfylde beskeden.';
	}
	return $errors;
}

function errorclass($errors, $key) {
	if (isset($errors[$key])) {
		return 'class = "error"';
	}
	return '';
}

function currentvalue($key, $attribute = TRUE) {
	if (!empty($_POST[$key])) {
		if ($attribute) {
			return 'value = "' . $_POST[$key] . '"';
		}
		else {
			return $_POST[$key];
		}
	}
	return '';
}

function setup() {
	global $config, $db;
	require 'settings.php';
	require 'libraries/Template.class.php';
	$db = new PDO('mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8', $config['db_user'], $config['db_pass']);

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
		':sender_ip' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? : $_SERVER['REMOTE_ADDR'],
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
	    'html'      => $mail['html'],
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
	global $config;
	$mail = array();
	$mail['to'] = $card['recipient_email'];
	$mail['toname'] = $card['recipient_name'];
	$mail['subject'] = strtr($config['mail_subject'], array(':sender_name' => $card['sender_name']));
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

/*
$card = get_card('60baa6ea1b54a39626a5837897347dcfc571d5b3');
print julekort_mail_html($card);
$mail = generate_card_mail($card);
print_r($mail);
send_mail($mail);*/