<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP';
use PHPMailer\PHPMailer\Exception;

// This line is REQUIRED to load the PHPMailer autoloader
include __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->Host = "smtp.gmail.com"; // Replace with the SMTP host we pick, here gmail (variable)
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587; // gmail port
$mail->Username = "your-gmail@gmail.com"; // Replace with the email we will use (variable)
$mail->Password = "your-gmail-password"; // Replace with password to that email (variable)

$mail->isHTML();

return $mail;