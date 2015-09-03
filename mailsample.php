<?php
require_once 'cmclib.php';

# mail setup recipients, subject etc
$recipients = "recipients@example.com";
$headers["From"] = "from@example.com";
$headers["To"] = "recipients@example.com";
$headers["Subject"] = "【自動返信】お問い合わせについて";
$mailmsg = "株式会社HDE\r\n"."山田太郎様\r\n"."\r\n".
    "この度は当社サービスへのお問い合わせ誠にありがとうございます。\r\n".
    "お問い合わせにつきまして折り返しご連絡いたします。\r\n".
    "今しばらくお時間頂けますよう宜しくお願いいたします。\r\n";
# SMTP server name, port, user/passwd
$smtpinfo["host"] = "subdomain.domain.com";
$smtpinfo["port"] = "25";
$smtpinfo["auth"] = true;
$smtpinfo["username"] = "username";
$smtpinfo["password"] = "password";

$mail_object = new Transport("smtp", $smtpinfo);
# send mail
$mail_object->send($recipients, $headers, $mailmsg);

?>
