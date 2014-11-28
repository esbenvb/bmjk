<?php

function create_form_validate($data) {
	$errors = array();
	var_dump($data);
	if (empty($data['recipient_name'])) {
		$errors['recipient_name'] = 'Du mangler at udfylde modtagerens navn.';
	}
	if (empty($data['sender_name'])) {
		$errors['sender_name'] = 'Du mangler at udfylde afsenderens navn.';
	}
	if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/', $data['recipient_email'])) {
		$errors['recipient_email'] = 'Indtast en gyldig modtager-mailadresse.';
	}
	if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/', $data['sender_email'])) {
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