<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


use Jaxon\Jaxon;
use Jaxon\Response\Response;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


// Load Composer's autoloader
//require 'vendor/autoload.php';
require_once ('class_freshdesk.php');
require_once ('class_account.php');


//Ajax Librairy ----
// and the Response class
// Get the core singleton object
$jaxon = jaxon();                        // Get the core singleton object   
$jaxon->register(Jaxon::USER_FUNCTION, 'resetPassword');
$jaxon->register(Jaxon::USER_FUNCTION, 'initResetPass');
$jaxon->register(Jaxon::USER_FUNCTION, 'updatePass');

function sendPasswordEmail($mailAddress,$mailBody){    
    global $lang;
    
    $mail = new PHPMailer();
    try {
        $mail->SMTPDebug = 0;        
        $mail->IsSMTP();
        $mail->Host = 'ssl://smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';
        //gmail address/password as user name
        $mail->Username = 'noreply@tpo.me';
        $mail->Password = 'TPOnoreply2018!';
        
        $mail->setFrom("noreply@tpo.me", "Your Concierge");
        $mail->addAddress($mailAddress);
        
        $mail->isHTML(true);
        $mail->Subject = "Your Concierge - ".$lang['PASSWORD_RESET'];
        $mail->Body = $mailBody; 
        
        $mail->send();
        $msg = $lang['PASSWORD_MAIL_SENT'];
        $log_content = $mailBody;
    }catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: ".$mail->ErrorInfo;
        $msg = $lang['STH_WENT_WRONG'];
        $log_content = $mail->ErrorInfo;
    }

    $request = "Mail: ".$mailAddress." - Result: ".$log_content;
    file_put_contents('log/log_resetpass_'.date("y-n-j").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [POST] '. $request.PHP_EOL, FILE_APPEND);

    return $msg;
}
function resetPassword($formInputs){
    global $lang;
    
    $response = new Response();
    $email = filter_var($formInputs['email'], FILTER_VALIDATE_EMAIL);
    //$email = $formInputs['email'];
    
    if (!isset($email) || empty($email)){
        $response->alert($lang['FILL_EMAIL']);
        return $response;
    }
    
   $account = new account();
   $existUser = $account->getAccount_byEmail($email);
   
    if (count($existUser) < 1){
        $msg = $lang['PASSWORD_UNKNOWN_MAIL'];
        $response->alert($msg);
        return $response;
    } else {
        $user_sel= $existUser[0];
    }
    
    $id = $user_sel->id;
    $key = $account->updateVericode($id);
    
    $url="https://".$_SERVER['SERVER_NAME']."/resetpass.php?mail=$email&key=$key";     
    
    $mailBody= $lang['PASSWORD_MAIL_HEADER']."
                <a href='$url'>$url</a>
                <br><br>".$lang['PASSWORD_MAIL_FOOTER'];

   $result = sendPasswordEmail($email,$mailBody);
   
    $response->alert($result);
    $response->redirect('login.php');
    return $response;
    
}

function initResetPass($email,$key){
    Global $lang;
    
    $response = new Response();
     
        
    if (!isset($email) || !isset($key) || empty($email) || empty($key)){
        
        $msg = $lang['STH_WENT_WRONG'];        
        $response->alert($msg);
        $response->redirect('forgotpass.php');
        return $response;
        
    }else{        
        $account = new account();    
        $validKey=$account->isValidKey($email, $key);
        
        if (!$validKey){
            $msg = $lang['STH_WENT_WRONG'];        
            $response->alert($validKey);
            $response->redirect('forgotpass.php');            
            return $response;
        }

        $content="<div class='form-group'>
                            <input type='password' name='password' id='password' class='form-control' placeholder='".$lang['FRM_PASSWORD']."' required >
                        </div>
                        <div class='form-group'>
                            <input type='password' name='cpassword' id='cpassword' class='form-control' placeholder='".$lang['FRM_C_PASSWORD']."' required >
                        </div>
                        <div class='invisible'>
                            <input type='text' name='email' id='email' class='form-control' value='".$email."'>
                        </div>
                        <div class='price-footer'>
                        <input type='button' onclick='jaxon_updatePass(jaxon.getFormValues(\"form_account\"));return false;' class='btn btn-skin-border mr-2 mb-2' value='".$lang['PASSWORD_RESET']."' />
                    </div>
                    </div>";
        $response->assign('contentForm', 'innerHTML', $content);
        return $response;
    }
}

function updatePass($formInputs){
    global $lang;
    $response = new Response();
    
    $password = $formInputs['password'];
    $cpassword = $formInputs['cpassword'];
    $email = $formInputs['email'];
    if (empty($password) || empty($cpassword) || $password != $cpassword){
        $response->alert($lang['PASSWORD_NO_MATCH']);
        return $response;
    }else{
        $account = new account();    
        $r = $account->updatePassword($email, $password);
        $msg = $lang['PASSWORD_UPDATED'];
        $response->alert($msg);
        $response->redirect('login.php');            
        return $response;
    }
}
