<?php 
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$email = $_POST["email"];

$token = bin2hex(random_bytes(50));

$token_hash = hash("sha256",$token);

$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

include "db_connect.php";

$sql = "UPDATE StudentAccount 
    SET reset_token_hash=?, 
        reset_token_expires_AT=? 
        WHERE email=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss",$token_hash,$expiry,$email);
$stmt->execute();

if ($conn->affected_rows ) {
   
    $mail = require "mailer.php";

    $mail->setFrom("noreply@brightstart.space");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset Request";
    $mail->Body = <<<END

    Click <a href="http://brightstart.space/reset_password.php?token=$token">here</a> 
    to reset your password.

    END;

    try {

        $mail->send();

    } catch (Exception $e) {

        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";

    }

}

echo "Message sent, please check your inbox.";