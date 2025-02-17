<?php

// Get the user's email
$email = $_POST["email"];

// Generate a random token
$token = bin2hex(random_bytes(16)); // Generate a random token
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30); // 30-minute expiration

$mysqli = require __DIR__ . "/database.php";

// Update the user's reset token and expiration
$sql = "UPDATE users 
    SET reset_token_hash = ?, 
        reset_token_expires_at = ? 
    WHERE email = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($mysqli->affected_rows === 0) {
    die("Email not found. Please check your email address.");
}

// Load the PHPMailer configuration
$mail = require __DIR__ . "/mailer.php";

// Set email details (variable)
$mail->setFrom("noreply@example.com");
$mail->addAddress($email);
$mail->Subject = "Password Reset Request";

// Replace "http://example.com/reset-password.php" with our actual URL (variable)
// Reset mail content
$mail->Body = <<<END
To reset your password, click this link: 
<a href="http://example.com/reset-password.php?token=$token">Reset Password</a>

If you did not request this password reset, you can safely ignore this email.
END;

try {
    $mail->send();
    echo "Password reset email has been sent to $email.";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
}
