<?php
require_once 'lib/swift_required.php';
require_once 'cmclib-swift.php';

Swift::init(function () {
    Swift_DependencyContainer::getInstance()
        ->register('mime.qpheaderencoder')
        ->asAliasOf('mime.base64headerencoder');

    Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
});

// Create the SMTP configuration
$transport = Swift_SmtpTransport::newInstance("subdomain.domain.com", 25);
$transport->setUsername("Username");
$transport->setPassword("Password");

// Create the message
$message = Swift_Message::newInstance();
$message->setTo(array(
       "toAddress@example.com" => "First Last",
      ));
$message->setSubject("【自動返信】お問い合わせについて");
$message->setBody("株式会社HDE\r\n"."山田太郎様\r\n"."\r\n"."この度は当社サービスへのお問い合わせ誠にありがとうございます。\r\n"."お問い合わせにつきまして折り返しご連絡いたします。\r\n"."今しばらくお時間頂けますよう宜しくお願いいたします。\r\n");
$message->setFrom("fromAddress@example.com", "First Last");

// Send the email
$mailer = new CMCMailer($transport);
$mailer->send($message, $failedRecipients);

// Show failed recipients
print_r($failedRecipients);

?>
