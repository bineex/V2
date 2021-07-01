<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendmail($to,$subject,$body){
    
    $mail = new PHPMailer;
    $mail->SMTPDebug = 0;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom("noreply@tpo.me", "Your Concierge");
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body = $body;
    $mail->IsSMTP();
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'ssl://smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Port = 465;

    //gmail address/password as user name
    $mail->Username = 'noreply@tpo.me';
    $mail->Password = 'TPOnoreply2018!';

    $is_sent = $mail->send();
    return $is_sent;
}

function logggger($txt,$type){
    file_put_contents('../log/'.$type.'_'.date("y-m-d").'.log', $txt.PHP_EOL, FILE_APPEND);
}

function convert_JPtoGMT($datetime){
    
    $AsiaTokyo = new DateTimeZone('Asia/Tokyo');
    $myDateTime = new DateTime($datetime, $AsiaTokyo);
    
    $gmtTimezone = new DateTimeZone('GMT');
    $myDateTime->setTimezone($gmtTimezone);
    
    return $myDateTime->format('Y-m-d\TH:i:s');
}
