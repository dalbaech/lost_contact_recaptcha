<?php
/* This file is a part of lost_contact_recaptcha and the use of it is governed by the terms set forth in the LICENSES file. */

$settings = array(
	"publickey" => "", //Public key created at: http://goo.gl/6eyN98
	"privatekey" => "", //Private key created at: http://goo.gl/6eyN98
	
	"title" => "Contact your admin!", //This is the title of the page
	"bgcolor" => "#A6A6A6", //This is the bgcolor to use
	
	"to" => "", //This is where the emails will go. email@domain.com
	"url_redirect" => "", //If you want to redirect to a custom "thank you for your message page", enter it's full URL (including http:// or https://) into this variable.) Otherwise, it'll use its own output page.
	"default_subject" => $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], //This sets the default subject line.
	
	"sanity" => "Remove this line to prove that you've entered the settings.",
	
	"recaptcha_errtxt" => "Incorrect reCAPTCHA solution! Try again.", //This is the error when it's an incorrect reCAPTCHA solution.
	"missing_vars_error" => "You have some needed variables missing. (All variables are required.) Missing ones are:", //This is the missing variables error.
	"theme" => "clean" //This is the theme option as specified at: http://goo.gl/OrKZvg
); 

include('recaptchalib.php');

if ($_POST) {
	foreach ($_POST as $key => $value) {
		${$key} = $value;
	} //$_POST as $key => $value
} //$_POST
if ($_GET) {
	foreach ($_GET as $key => $value) {
		${$key} = $value;
	} //$_GET as $key => $value
} //$_GET
foreach ($settings as $key => $value) {
	${$key} = $value;
} //$settings as $key => $value

$error = "";

function display_email($settings)
{
	foreach ($settings as $key => $value) {
		${$key} = $value;
	} //$settings as $key => $value
	
	echo "<html><head><title>$title</title></head>
            <body bgcolor=$bgcolor>
                Use the following form to contact the administrator.<BR><BR>
        <script type=\"text/javascript\">
                var RecaptchaOptions = {
                theme : '$theme'
                };
                 </script>
                <form method=\"post\" action=\"\">
                <table border=1>
                <tr><td>Name:</td><td><input type=\"text\" name=\"name\" size=50></td></tr>
                <tr><td>Email address:</td><td><input type=\"text\" name=\"email\" size=50></td></tr>
                <tr><td>Subject:</td><td><input type=\"text\" name=\"subject\" value=\"Email from: $default_subject\" size=50></td></tr>
                <tr><td>Message:</td><td><textarea rows=\"5\" cols=\"50\" name=\"message\"></textarea></td></tr>
                <tr><td>reCAPTCHA verification:</td><td>" . recaptcha_get_html($publickey, $error, $use_ssl) . "</td></tr>
                <tr><td></td><td><input type=\"submit\" value=\"Send email\" /></td></tr></table>
              </form>";
}

function display_email2($settings, $name, $email, $subject, $message)
{
	foreach ($settings as $key => $value) {
		${$key} = $value;
	} //$settings as $key => $value
	echo "<html><head><title>$title</title></head>
            <body bgcolor=$bgcolor>
                <script type=\"text/javascript\">
                var RecaptchaOptions = {
                theme : '$theme'
                };
                 </script>
                <form method=\"post\" action=\"\">
                <table border=1>
                <tr><td>Name:</td><td><input type=\"text\" size=50 name=\"name\" value=\"$name\"></td></tr>
                <tr><td>Email address:</td><td><input type=\"text\" size=50 name=\"email\" value=\"$email\"></td></tr>
                <tr><td>Subject:</td><td><input type=\"text\" size=50 name=\"subject\" value=\"$subject\"></td></tr>
                <tr><td>Message:</td><td><textarea rows=\"5\" cols=\"50\" name=\"message\">$message</textarea></td></tr>
                <tr><td>reCAPTCHA verification:</td><td>" . recaptcha_get_html($publickey, $error, $use_ssl) . "</td></tr>
                <tr><td></td><td><input type=\"submit\" value=\"Send email\" /></td></tr></table>
              </form>";
}

function send_email($settings, $name, $email, $subject, $message)
{
	foreach ($settings as $key => $value) {
		${$key} = $value;
	} //$settings as $key => $value
	$headers = "From: $name <$email>" . $eol;
	foreach ($_SERVER as $key => $aline) {
		$server_vars .= $key . ": $aline \n";
	} //$_SERVER as $key => $aline
	$message2 = "$message \n\n\n\n---------------------------------\nThe following \$_SERVER variables were set:\n$server_vars";
	
	if (mail($to, $subject, $message2, $headers)) {
		if (!empty($url_redirect)) {
			header("Location: $url_redirect");
		} //!empty($url_redirect)
		if (empty($url_redirect)) {
			$string_out = "Thanks for the message. It has been sent!

The following is the message details:
From: $name <$email>
Subject: $subject

Message: $message";
		} //empty($url_redirect)
		$string_out = htmlspecialchars($string_out);
		$string_out = str_replace("\n", "<BR>\n", $string_out);
		echo $string_out;
	} //mail($to, $subject, $message2, $headers)
}
