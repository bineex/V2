<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

chdir(dirname(__FILE__));
$_SERVER['HTTP_HOST'] = '';
$NOSESSION = 1;

require_once ('../../../vendor/autoload.php');
require_once ('../init.php');

use PHPMailer\PHPMailer\PHPMailer;

require_once '../classes/phpmailer/PHPMailer.php';
require_once '../classes/phpmailer/Exception.php';


$db = DB::getInstance();

function flag_mail_sent($id_event_attds){
    global $db;
    $sql= "update event_attendees set mail_sent_at = now() where id_event_attds = $id_event_attds";
    $query = $db->query($sql);
    echo $sql."\n\r";
    return $query;
}

function send_reminder(){
    global $db; global $lang;
    
    $sql="SELECT distinct att.id_event_attds,CONVERT_TZ(now(),'+00:00','+09:00') AS JPN,usr.lname,email,art.title,art.language,date_start,att.id_ticket,att.id_user,att.id_event_schedule
        FROM event_attendees att
        join event_schedule sch on att.id_event_schedule = sch.id_event_schedule
        join fd_articles art on sch.id_event = art.article_id and att.language = art.language
        join users usr on att.id_user = usr.fd_id
        where deleted_at is null and date_start = date_add(date(CONVERT_TZ(now(),'+00:00','+09:00')),interval 1 day) and mail_sent_at is null
        order by article_id, att.id_user";
    
    $db->query($sql);
    $r = $db->results();
    $i = 0;
    
    $mail = new PHPMailer;
    $mail->SMTPDebug = 0; // 2 SMTP::DEBUG_SERVER; 0 SMTP::DEBUG_OFF
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
    
    foreach ($r as $values) {
        $email = $values->email; 
       
        $mailSubject_en = "【YourConcierge】Reminder Event Reservation & Details - $values->title";        
        $mailBody_en= "Dear ".$values->lname."-sama,
                    <br><br>
                    This is a friendly reminder that you have booked an event with us! Thank you!<br>
                    We’re re-sending you the details concerning:
                    <br><br>
                    ".$values->title."
                    <br><br>
                    You can find the event meeting room URL and other information through the following link (please login to the portal): 
                    <br><br>
                    https://card.yourconcierge.jp/request.php?rqt=".$values->id_ticket."
                    <br><br>
                    * We are currently not sending separate emails and all information can be found through our portal and corresponding requests. 
                    <br><br>
                    May you have any questions for the instructor or something you want to tell us in advance, please contact us through the comment field of the corresponding event reservation.
                    <br><br>
                    In case you need to cancel or change your reservation, please do so from the event page.
                    <br>
                    <br><br>
                    We look forward to seeing you =).
                    <br><br>
                    Sincerely,<br>
                    YourConcierge
                    <br><br>
                    <hr>
                    *This e-mail was sent from a send-only address.
                    ";
        
        $mailSubject_jp = "【YourConcierge】明日開催！ご参加予定のイベント - $values->title";
        $mailBody_jp = $values->lname."様
                    <br><br>
                    こんにちは。YourConciergeです。<br>
                    この度は下記のイベントをご予約いただき、ありがとうございます。
                    <br><br>
                    ■ ご予約内容 ■
                    <br><br>
                    ".$values->title."
                    <br><br>
                    ■ オンラインイベントの参加方法 ■<br>
                    下記のURLにログインし、ご確認ください。<br>
                    https://card.yourconcierge.jp/request.php?rqt=".$values->id_ticket."
                    <br><br>
                    ＊ご予約内容は、ポータルサイトの利用履歴・イベントページからご確認いただけるため、参加方法に関する個別のご案内は行っておりませんのでご注意ください。
                    <br><br>
                    ＊講師へのご質問や、事前にお伝え頂くことがあれば、コメントボックスよりご連絡ください。<br>
                    ＊キャンセルの場合もイベントページよりお手続きください。
                    <br><br>
                    明日、".$values->lname."様にお会いできます事を楽しみにしています。
                    <br><br>
                    YourConcierge
                    <br><br>
                    <hr>
                    *本メールは送信専用アドレスよりお送りしています。<br>
                    ご返信いただいても内容の確認・ご返答ができませんので、ご了承ください。";
        
         if (isset($values->language) && $values->language == 'en'){
            $mailSubject = $mailSubject_en;
            $mailBody = $mailBody_en;
        }
        else {
            $mailSubject = $mailSubject_jp;
            $mailBody = $mailBody_jp;
        }

        $mail->Body = $mailBody;
        $mail->Subject = $mailSubject;
        
        try {
            $mail->addAddress($email, $values->lname);
        } catch (Exception $e) {
            $msg = 'Invalid address skipped: ' . htmlspecialchars($email);
            
            file_put_contents('../public/log/log-notification_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - '.$values->id_user.' - '.$values->id_event_schedule.' - '.$msg.PHP_EOL, FILE_APPEND);
            echo ($msg."\n\r");
            continue;
        }
        
        try {
            //$mail->send();
            $msg = " Sent - ".htmlspecialchars($email);
            $flag = flag_mail_sent($values->id_event_attds);
            //Mark it as sent in the DB
        } catch (Exception $e) {
            echo 'Exception reçue : ',  $e->getMessage(), "\n";
            $msg = htmlspecialchars($email)." - Error: " . $mail->ErrorInfo;
            //Reset the connection to abort sending this message
            //The loop will continue trying to send to the rest of the list
            $mail->getSMTPInstance()->reset();
        }
        //Clear all addresses for the next iteration
        $mail->clearAddresses();

        file_put_contents('../public/log/log-notification_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - '.$values->id_user.' - '.$values->id_event_schedule.' - '.$msg.PHP_EOL, FILE_APPEND);
        echo ($msg."\n\r");
        $i++;
        if($i > 20){
            sleep(120);
            $i = 0;
        }
        else{
            sleep(2);
        }
    }
    
     return $i;
}

$result = send_reminder();

