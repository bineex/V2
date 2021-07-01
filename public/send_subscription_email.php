<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

chdir(dirname(__FILE__));
$_SERVER['HTTP_HOST'] = '';
$NOSESSION = 1;

require_once ('../vendor/autoload.php');
require_once ('../init.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function send_welcome($to_mail,$to_name){
    global $lang;

    $body = file_get_contents('welcome_subscription_'.$lang['LANGUAGE_ID'].'.html');
    $body = str_replace('{{Name}}', $to_name, $body);

    $mail = new PHPMailer;
    $mail->SMTPDebug = 2; // 2 SMTP::DEBUG_SERVER; 0 SMTP::DEBUG_OFF
    $mail->CharSet = 'UTF-8';
    $mail->setFrom("noreply@tpo.me", "Your Concierge");               
    $mail->isHTML(true);        
    $mail->IsSMTP();
    $mail->SMTPKeepAlive = true;
    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'ssl://smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Port = 465;
    //gmail address/password as user name
    $mail->Username = 'noreply@tpo.me';
    $mail->Password = 'TPOnoreply2018!';

    $email = 'moreno@bineex.com';
    $to = 'Mario Moreno';

    $mail->Body = $body;
    $mail->Subject = 'Welcome to YourConcierge & You';
        
    $mail->addAddress($to_mail,$to_name);
    try {
        $mail->send();
        $msg = " Sent - ".htmlspecialchars($email);

    } catch (Exception $e) {
        echo 'Exception reçue : ',  $e->getMessage(), "\n";
        $msg = htmlspecialchars($email)." - Error: " . $mail->ErrorInfo;
        //Reset the connection to abort sending this message
        //The loop will continue trying to send to the rest of the list
        $mail->getSMTPInstance()->reset();
    }
        //Clear all addresses for the next iteration
        $mail->clearAddresses();

        //file_put_contents('../public/log/log-notification_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - '.$values->id_user.' - '.$values->id_event_schedule.' - '.$msg.PHP_EOL, FILE_APPEND);
        echo ($msg."\n\r");
    
     return TRUE;
}

$result = send_welcome('mkt@bineex.com','makt Moreno');
