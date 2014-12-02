<?php
setup();
function create_form_validate($data) {
	require "rfc822.php";
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
	$params += $mail['extra'];

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
	curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

	// obtain response
	$response_json = curl_exec($session);
	curl_close($session);

	// print everything out
	$response = json_decode($response_json);
	//print_r($response);
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
	$mail['extra']['files[mail.gif]'] = '@mail.gif';
	$mail['extra']['content[mail.gif]'] = md5('mail.gif');
	return $mail;
}

function julekort_mail_html($card) {
	global $config;
	$card_url = $config['base_url'] . '/card.php?card_sha=' . $card['sha1'];
	$image_url = 'cid:' . md5('mail.gif');
	$card_html = <<<END
	<html>
	<body>
		Click <a href="$card_url">here</a> to read the mail. <br />
		<a href="$card_url"><img src="$image_url" /></a>
	</body>
	</html>

END;
	return $card_html;
}
function julekort_mail_text($card) {
	global $config;
	$card_url = $config['base_url'] . '/card.php?card_sha=' . $card['sha1'];
	$card_text = <<<END
	You have received a christmas card. It can be read at this URL:
	$card_url
END;
	return $card_html;	
}
/*
$card = get_card('60baa6ea1b54a39626a5837897347dcfc571d5b3');
print julekort_mail_html($card);
$mail = generate_card_mail($card);
print_r($mail);
send_mail($mail);*/