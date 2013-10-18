<?php

/*
 * This is a script that uses reCAPTCHA to verify that a human is submitting a contact form.
 *
 * Copyright (c) 2013 Daniel Carll -- http://www.randomlylost.com/software
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

include("common.php");
if (isset($sanity)) {
	die("You need to modify common.php and remove the sanity line from the settings array after modifying the array to your proper settings.");
} //isset($sanity)

//Force SSL if the server supports it.
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'off') {
	header('Strict-Transport-Security: max-age=31536000');
	header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], true, 301);
	die();
} //isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'off'

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
	$use_ssl = 1;
} //isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'
elseif (!isset($_SERVER['HTTPS'])) {
	$use_ssl = 0;
} //!isset($_SERVER['HTTPS'])
if (!$_POST) {
	display_email($settings);
} //!$_POST

if ($_POST) {
	if ($name && $email && $subject && $message) {
		
		$resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		if (!$resp->is_valid) {
			if ($resp->error == "incorrect-captcha-sol") {
				$error2 = $recaptcha_errtxt;
				echo "Error with reCAPTCHA: <FONT COLOR=RED>$error2</FONT><BR>";
			} //$resp->error == "incorrect-captcha-sol"
			else {
				$error2 = $resp->error;
				echo "Error with reCAPTCHA: <FONT COLOR=RED>$error2</FONT>";
			}
			$title = "$error2 | $title";
			display_email2($settings, $name, $email, $subject, $message);
		} //!$resp->is_valid
		
		else {
			send_email($settings, $name, $email, $subject, $message);
		}
		
	} //$name && $email && $subject && $message
	
	else {
		if (!$name) {
			$missing .= "Name ";
		} //!$name
		if (!$email) {
			$missing .= "Email ";
		} //!$email
		if (!$subject) {
			$missing .= "Subject ";
		} //!$subject
		if (!$message) {
			$missing .= "Message ";
		} //!$message
		echo "<FONT COLOR=RED>$missing_vars_error $missing</FONT><BR><BR>";
		display_email2($settings, $name, $email, $subject, $message);
	}
} //$_POST
