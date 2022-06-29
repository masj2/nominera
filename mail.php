<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);// Passing `true` enables exceptions
try {
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'admin@ungpirat.org';
    $mail->Password = '';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
	$mail->CharSet = 'UTF-8';
    $mail->setFrom('admin@ungpirat.org', 'Ung pirat');
    $mail->addAddress($_SESSION['email']);
    //$mail->addBCC('kansli@ungpirat.se');

    $mail->isHTML(true);
    $mail->Subject = 'Ung Pirat';
    $mail->Body = $_SESSION['message'];
    $mail->send();
    header("Location: index.php");
} catch (Exception $e) {
	if($_SESSION['email']==""){
		header("Location: index.php");
	}else{
		echo 'Nominering lyckades men det blev problem att mejla ut uppgifterna till den nominerade, kontakta fs@ungpirat.se med följande info ';
		echo 'Error: ' . $mail->ErrorInfo;
	}
}
$_SESSION['email']="";
$_SESSION['message']="";
?>