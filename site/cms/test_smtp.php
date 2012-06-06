<?php

//error_reporting(E_ALL);
error_reporting(E_STRICT ^ E_NOTICE);

//date_default_timezone_set('America/Toronto');
//date_default_timezone_set(date_default_timezone_get());
date_default_timezone_set("America/New_York");

include_once('_phpMailer/class.phpmailer.php');
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail             = new PHPMailer();

$body             = "<b>html body</b>";

$mail->IsSMTP(); // telling the class to use SMTP
$mail->SMTPAuth = true;
$mail->Host       = "relay-hosting.secureserver.net"; // SMTP server
$mail->Username = "photos@parkcareers.com";
$mail->Password = "tomcoke";

$mail->From       = "photos@parkcareers.com";
$mail->FromName   = "Do Not Reply";

$mail->Subject    = "PHPMailer Test Subject via smtp";

$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);

$mail->AddAddress("coakleytom@hotmail.com", "tom");

//$mail->AddAttachment("images/phpmailer.gif");             // attachment

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
  print "<p>host: " . $mail->Host.
    "<br>SMTPAuth: " . $mail->SMTPAuth .
    "<br>Username: " . $mail->Username .
    "<br>Password: " . $mail->Password .
    "<br>From: " . $mail->From .
    "<br>FromName: " . $mail->FromName;
} else {
  echo "Message sent!";
}

?>
