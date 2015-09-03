<?php
require_once 'cmclib.php';

/* mail setup recipients, subject etc */
$recipients = "wilson.fonda@g.hde.co.jp";
$headers["From"] = "wilson@smtps.jp";
$headers["To"] = "wilson.fonda@g.hde.co.jp";
$headers["Subject"] = "User feedback";
$mailmsg = "Hello, This is a test.";
/* SMTP server name, port, user/passwd */
$smtpinfo["host"] = "smtp.subdomain.smtps.jp";
$smtpinfo["port"] = "10025";
$smtpinfo["auth"] = true;
$smtpinfo["username"] = "username";
$smtpinfo["password"] = "password";

$mail_object = new Transport("smtp");
$mail_object = new Transport("smtp", $smtpinfo);
/* Ok send mail */
$mail_object->send($recipients, $headers, $mailmsg);

?>
