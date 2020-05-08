<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once ('class_freshdesk.php');
require_once ('fct_survey.php');
require_once ('fct_security.php');
//require_once ('fct_misc_tools.php');

use Jaxon\Jaxon;
use Jaxon\Response\Response;

//Ajax Librairy ----
// and the Response class
// Get the core singleton object


$jaxon = jaxon();                        // Get the core singleton object   
$jaxon->register(Jaxon::USER_FUNCTION, 'displayUsers'); // Register the function with Jaxon
$jaxon->register(Jaxon::USER_FUNCTION, 'displayUser');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayTest');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayTicketList');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayTicket');
$jaxon->register(Jaxon::USER_FUNCTION, 'displaySurvey');
$jaxon->register(Jaxon::USER_FUNCTION, 'getSurveyTempValue',array('mode' => "'synchronous'"));
$jaxon->register(Jaxon::USER_FUNCTION, 'setSurveyTempValue',array('mode' => "'synchronous'"));
$jaxon->register(Jaxon::USER_FUNCTION, 'saveSurveyValues');
        
$jaxon->register(Jaxon::USER_FUNCTION, 'newTicket');
$jaxon->register(Jaxon::USER_FUNCTION, 'newTicket2');
$jaxon->register(Jaxon::USER_FUNCTION, 'closeTicket');
$jaxon->register(Jaxon::USER_FUNCTION, 'replyTicket');
$jaxon->register(Jaxon::USER_FUNCTION, 'cancelFormTicket');
$jaxon->register(Jaxon::USER_FUNCTION, 'cancelFormTicketEnhanced');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayProducts');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayFolders');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayArticle');

$jaxon->register(Jaxon::USER_FUNCTION, 'closeModal');
$jaxon->register(Jaxon::USER_FUNCTION, 'initPage');

 function insertTicketCustomer($ticket_id,$customer_id){
     $db = DB::getInstance();
     $db->insert("ticket_customers", ["ticket_id"=>$ticket_id,
                                        "customer_id"=>$customer_id]);
 }
 

function checkCompanyUser($user_id){
    
}
function cancelFormTicket(){
    $response = new Response();
    $response->assign('sendermessage', 'value', '');
    $response->assign('sendersubject', 'value', '');
    return $response;
}
function cancelFormTicketEnhanced(){
    $response = new Response();    
    $response->redirect('index.php');
    return $response;
}
function getTicketCompany($idTicket){
    $freshTickets= new freshdesk();
    $detailTicket = $freshTickets->getTicketDetail($idTicket);
    
    $idCompany=$detailTicket['company_id'];
    return $idCompany;
}
function newTicket($contact_id,$fields){
    Global $lang;
    
    $response = new Response();
    $sendersubject = strip_tags(trim($fields["sendersubject"]));
    $sendermessage = strip_tags(trim($fields["sendermessage"]));
    
    if(empty($sendersubject)){
        $errors[] = $lang['REQUEST_FILL_SUBJECT'];
        $response->alert($lang['REQUEST_FILL_SUBJECT']);
        return $response;
    }
    if(empty($sendermessage)){
        $errors[] = $lang['REQUEST_FILL_MSG'];
        $response->alert($lang['REQUEST_FILL_MSG']);
        return $response;
    }
   

    $content['requester_id'] = intval($contact_id);
    $content['source'] = 2; // 2=> portal
    $content['status'] = 2; // 2=> Open
    $content['priority'] = 2; // 2=> Medium
    $content['subject'] = $sendersubject;
    $content['description'] = $sendermessage;
    
    $customField['cf_category_level_1'] = "PENDING";
    $customField['cf_sub_category'] = "PENDING";
    
    //$customField['cf_category'] = "PENDING";
    $customField['cf_travel_required'] = "No";
    $customField['cf_type_of_request'] = "No";
    $customField['cf_request_source'] = "Request Form（オンライン）";

    
    $content['custom_fields']=$customField;
    
    file_put_contents('log/log-ticket_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [REQT] '. json_encode($content,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
    $freshUsers= new freshdesk();    
    $newTicket = $freshUsers->addTicket($content);
    
    file_put_contents('log/log-ticket_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [RESP] '. json_encode($newTicket,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
    
     if (isset($newTicket['id']) && !empty($newTicket['id'])){
         $response->alert($lang['REQUEST_CREATED']);
     }
    else{
        $response->alert($lang['STH_WENT_WRONG']);
    }
    
    $script="jaxon_cancelFormTicket();jaxon_displayTicketList('$contact_id');";
    $response->script($script);
    return $response;
    
}

function newTicket2($contact_id,$fields){
    Global $lang;
    $response = new Response();
    
    $subject = "";
    $body = "";
    
    $guests_list = " <br>ゲスト出席者 : ";    
    if (isset($_SESSION['Guests']) && !empty($_SESSION['Guests'])){ 
        foreach ($_SESSION['Guests'] as $key => $value) {
                $guests_list .= $value."<br>";
            }
    } else {
        $response->alert('ゲストを入力してください。');
         return $response;
    }
    
    if (isset($fields['participant']) && !empty($fields['participant'])){ $body = '貴社出席者 : '.$fields['participant']."<br>"; }
    $body .= $guests_list."<br>";
    
    if (isset($fields['request_type']) && !empty($fields['request_type'])){ $subject = $fields['request_type']; }
    
    if (isset($fields['input_date']) && !empty($fields['input_date'])){        
        $subject.= " - ".$fields['input_date'];        
        $input_date = isDate($fields['input_date']);
        
        if ($input_date != false){
            $customField['cf_calendar_date'] = $input_date;
            $body .= '日にち：'. $input_date."<br>";
        } else {
            $customField['cf_date'] = $fields['input_date'];
            $body .= '日にち：'.$fields['input_date']."<br>";
        }        
    }
    
    if (isset($fields['input_time']) && !empty($fields['input_time'])){
        $customField['cf_time'] = $fields['input_time']; 
        $body .= '時間：'.$fields['input_time']."<br>";
    }
    if (isset($fields['input_nbpeople']) && !empty($fields['input_nbpeople'])){
        $customField['cf_number_of_people'] = $fields['input_nbpeople'];
        $body .= '人数：'.$fields['input_nbpeople']."<br>";
    }
    if (isset($fields['input_budjet']) && !empty($fields['input_budjet'])){
        $customField['cf_budget_for_restaurant_catering'] = $fields['input_budjet'];
        $body .= '予算：'.$fields['input_budjet']."<br>";
    }
    if (isset($fields['input_area']) && !empty($fields['input_area'])){
        $customField['cf_area'] = $fields['input_area'];
        $body .= '場所：'.$fields['input_area']."<br>";
    }
    if (isset($fields['private_room']) && !empty($fields['private_room'])){
        $body .= ' 個室 : '.$fields['private_room']."<br>";        
    }
    if (isset($fields['restaurant_reservation']) && !empty($fields['restaurant_reservation'])){
        $body .= ' 予約希望 : '.$fields['restaurant_reservation']."<br>";        
    }
    if (isset($fields['input_desc']) && !empty($fields['input_desc'])){
        $body .= $fields['input_desc']."<br>";
    }
    
    
    $content['requester_id'] = intval($contact_id);
    $content['source'] = 2; // 2=> portal
    $content['status'] = 2; // 2=> Open
    $content['priority'] = 2; // 2=> Medium
    $content['subject'] = $subject;
    $content['description'] = $body;

    //$customField['cf_category'] = "PENDING";
    $customField['cf_category_level_1'] = "PENDING";
    $customField['cf_sub_category'] = "PENDING";
    
    $customField['cf_category_level_1'] = "PENDING";
    $customField['cf_sub_category'] = "PENDING";
    
    $customField['cf_travel_required'] = "No";
    $customField['cf_type_of_request'] = "No";
    $customField['cf_request_source'] = "Request Form（オンライン）";
    
    $content['custom_fields'] = $customField;

    file_put_contents('log/log-ticket_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [REQT] '. json_encode($content,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
    
    $freshUsers= new freshdesk();    
    $newTicket = $freshUsers->addTicket($content);
    
    file_put_contents('log/log-ticket_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [RESP] '. json_encode($newTicket,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
     
     if (isset($newTicket['id']) && !empty($newTicket['id'])){
        $response->alert($lang['REQUEST_CREATED']);
        $ticket_id = $newTicket['id'];
        foreach ($_SESSION['Guests'] as $key => $value) {
            $customer_id = $key;
            insertTicketCustomer($ticket_id,$customer_id);
            //file_put_contents('log/log-guests_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').';'.$newTicket['id'].';'.$key.';'.$value.PHP_EOL, FILE_APPEND);
        }        
     }
     else {
        $response->alert($lang['STH_WENT_WRONG']);
    }
    
    //$script="jaxon_cancelFormTicket();jaxon_displayTicketList('$contact_id');";
    //$response->script($script);
    $response->redirect('index.php');
    return $response;
    
}

function initPage($contact_id,$ticket_id = 0){
    
    $response = new Response();
    $script="jaxon_displayTicketList('$contact_id','$ticket_id');";
    $response->script($script);
    return $response;
    
}
function displayUsers(){
    $freshUsers= new freshdesk();    
    $tabUsers = $freshUsers->getUsers('resetpasswordtest@bineex.com');
    //freshtest@tpo.me
    $display="<table class='table table-hover'>
		  <thead>
			<tr>
			  <th scope='col'>name</th>
			  <th scope='col'>email</th>
			  <th scope='col'>company</th>
                          <th scope='col'></th>
			</tr>
		  </thead>
		  <tbody>";
			
    foreach ($tabUsers as $key=>$values){
        $name=$values['name'];
        $email=$values['email'];
        $company=$values['id'];
        $id=$values['id'];
        $img=$values['avatar'];
        //$company[strval($values['company_id'])]['name'];
        $display.="<tr>
                    <th scope='row' onclick='jaxon_displayTicketList(\"".$email."\");return false;'>$name</th>
                    <td>$email</td>
                    <td>$company</td>
                    <td>$id</td>
                  </tr>
                <tr>
                    <td colspan=3><img alt='Mario Moreno' data-test-user='user-avatar' src='https://s3.amazonaws.com/cdn.freshdesk.com/data/helpdesk/attachments/production/35013440182/original/imagesg.jpeg?X-Amz-Algorithm=AWS4-HMAC-SHA256&amp;X-Amz-Credential=AKIAJ2JSYZ7O3I4JO6DA%2F20180530%2Fus-east-1%2Fs3%2Faws4_request&amp;X-Amz-Date=20180530T120013Z&amp;X-Amz-Expires=86400&amp;X-Amz-Signature=cadc4b5fa0f23707b7f2b965f58d2446aa23624567c6055eae285996a8818d84&amp;X-Amz-SignedHeaders=Host' class='profilepic__img avatar-block'></td>
                </tr>
                <tr>
                   
                </tr>";
        
    }
    $display.="</tbody></table>";
    
    $response = new Response(); // Instance the response class 
    $response->assign('contentUsers', 'innerHTML', $display);// Invoke the alert method
    return $response;          // Return the response to the browser

}
function displayUser($user_id){
    $freshUsers= new freshdesk();    
    $values = $freshUsers->getUser($user_id);
    $display="<table class='table table-hover'>";
    
        $name=$values['name'];
        $email=$values['email'];
        $company=$values['id'];
        $id=$values['id'];
        $img=$values['avatar']['avatar_url'];
        $custom="";
        foreach ($values['custom_fields'] as $key => $val) {
            $custom .= $key .": " .$val . " - ";
        }
        //$gender=$values['custom_fields']['性別(Gender)'];
        //$company[strval($values['company_id'])]['name'];
        $display.="<tr>
                    <td rowspan=5><img alt='$name' data-test-user='user-avatar' src='$img'></td>
                   </tr>
                   <tr>
                    <th scope='row' onclick='jaxon_displayTicketList(\"".$email."\");return false;'>$name</th>
                   </tr>
                   <tr>
                    <td>$email</td>
                   </tr>
                   <tr>
                    <td>$custom</td>
                   </tr>
                   <tr>
                    <td>$id</td>
                  </tr>";
        
    $display.="</table>";
    
    $response = new Response(); // Instance the response class 
    $response->assign('contentUsers', 'innerHTML', $display);
    return $response;          // Return the response to the browser

}
function displayTicketList($user,$ticket_id = 0){
    
    $ticket_selected = FALSE;
    $freshTickets= new freshdesk();    
    $tabTickets = $freshTickets->getTicketList($user);

    $statusList[2] = 'Open';
    $statusList[3] = 'Pending';
    $statusList[4] = 'Resolved';
    $statusList[5] = 'Closed';
    $statusList[8] = 'Booked';
    $statusList[17] = 'Booked';
    $statusList[19] = 'Cancelled';
    
    $display = "<table class='table table-hover table-sm'>
            <tbody>";

    foreach ($tabTickets as $key=>$values){
        if (intval($values['association_type']) < 2){
            $ticket = array(
                    'id' => $values['id'],
                    'user' => $user,
                    'subject' => $values['subject'],
                    'status' => $values['status']);

            if ($ticket_id == $values['id']){$ticket_selected = $values['id'];}

            $date = new DateTime($values['created_at']);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $date_created = $date->format('Y-m-d H:i');

            $subject=$values['subject'];
            $display_id=$values['id'];

            $status='';
            if (isset($statusList[$values['status']])){
                $status = $statusList[$values['status']];
                if ($statusList[$values['status']] == 'Open' || $statusList[$values['status']] == 'Booked'){
                    $status = "<span class='badge badge-success'>".$statusList[$values['status']]."</span>";
                    $subject= "<th scope='row' >$subject</th>";
                }
                elseif ($statusList[$values['status']] == 'Closed' || $statusList[$values['status']] == 'Cancelled' ){
                    $status = "<small class='text-muted'>".$statusList[$values['status']]."</small>";
                    $subject= "<td scope='row' >$subject</td>";
                }
                //<h3><span class="badge badge-success">Label</span></h3>
            }
            else{
                $status = $values['status'];
            }
            //$jsonTicket = json_encode($ticket,JSON_HEX_APOS);
            $display .= "<tr onclick='jaxon_displayTicket(".$values['id'].");'>
              <td scope='row' >$display_id</td>
              <th scope='row' >$subject</th>
              <td scope='row' >$date_created</td>
              <td scope='row' >$status</td>

            </tr>";
        }
    }
    $display .= "</tbody></table>";
    $response = new Response();
    
    $response->assign('contentTickets', 'innerHTML', $display);
    
    if ($ticket_selected){       
        
        $script="jaxon_displayTicket(".json_encode($ticket_selected).");";      
        $response->script($script);
    }
    else {
        $List = getListSurveyPending();
        
        if (count($List) > 0){
            $script="jaxon_displaySurvey(".json_encode($List,JSON_HEX_APOS).");";
            $response->script($script); 
        }
   }

    return $response;

}

function getSurveyTempValue($id_ticket){
    
    $rate = -1;
    if (isset($_SESSION['Survey'][$id_ticket])){ $rate = $_SESSION['Survey'][$id_ticket]; }
    
    $response = new Response();
    $response->setReturnValue($rate);
    return $response;
}
function setSurveyTempValue($id_ticket,$valueRate){
    
    $_SESSION['Survey'][$id_ticket] = $valueRate;
    
    $response = new Response();
   
    $response->setReturnValue(TRUE);
    return $response;
}
function saveSurveyValues($comments){
    if (isset($_SESSION['Survey'])){
        $RateValue = array( 0 => -103,                           
                            1 => 100,
                            2 => 103);
       
        foreach($_SESSION['Survey'] as $key => $value){

            $ratings['default_question'] = $RateValue[$value];
            $content['ratings'] = $ratings;
            if (isset($comments['comment'.$key]) && !empty($comments['comment'.$key])){
                $content['feedback'] = $comments['comment'.$key];
            }

            $freshUsers = new freshdesk();    
            $newRating = $freshUsers->postStatisfactionRating($key,$content);

            flagSurveyDisplayed($key,$RateValue[$value]);
        }
        
    unset($_SESSION['Survey']);
    }
    $response = new Response();
    //$response->alert(print_r($_SESSION['Survey'],true));
    $response->script('$("#modalp .closeModal").click()');
    return $response;
    
}
function displaySurvey($listSurvey){
    Global $scriptJQueryLoad; Global $lang;
    unset($_SESSION['Survey']);
    $title = $lang['SURVEY_QUESTION_DEFAULT'];
    
    $scriptRating= "
            $('.fa').on('click', function () {
               var id_ticket = $(this).data('ticket');
               var obj = '.t'+id_ticket ;
               ratedIndex = parseInt($(this).data('index'));
               $('#'+id_ticket).val(ratedIndex);
               jaxon_setSurveyTempValue(id_ticket, ratedIndex);
            });

            $('.fa').mouseover(function () {
                var id_ticket = $(this).data('ticket');                
                var obj = '.t'+id_ticket ;
                var currentIndex = parseInt($(this).data('index')); 
                $(obj).css('color', '#e6e6e6');
                if (currentIndex == 0){
                    $(obj+':eq('+currentIndex+')').css('color', '#ff8c00');}
                else if (currentIndex == 1){
                    $(obj+':eq('+currentIndex+')').css('color', '#ffed00');}
                else if (currentIndex == 2){
                    $(obj+':eq('+currentIndex+')').css('color', '#00b400');}
            });

            $('.fa').mouseleave(function () {
                var id_ticket = $(this).data('ticket');
                var obj = '.t'+id_ticket ;                
                var id_sel = parseInt($('#'+id_ticket).val());
                
                var set_color;
                if(id_sel == 0){set_color = '#ff8c00';}
                else if(id_sel == 1){set_color = '#ffed00';}
                else if(id_sel == 2){set_color = '#00b400';}
                
                var i;
                for (i = 0; i < 3; i++) {
                    if (i == id_sel){
                        $(obj+':eq('+i+')').css('color', set_color);}
                    else {
                        $(obj+':eq('+i+')').css('color', '#e6e6e6');}                    
                }                
            });";
    
    $bodySurvey = '<form id="form_survey" name="form_survey" onsubmit="return jaxon_saveSurveyValues(jaxon.getFormValues(\'form_survey\'))">';
    
    foreach ($listSurvey as $key => $request) {        
       $subject = $request['subject'];
       $id = $request['id_ticket'];
       $bodySurvey.='<div class="row justify-content-between">
                        <div class="col-8"><b>'.$subject.'</b></div>
                        <div class="col-4">
                            <i data-ticket="'.$id.'" class="fa fa-frown-o fa-3x t'.$id.'" style="color: #ff8c00;" data-index="0"></i>
                            <i data-ticket="'.$id.'" class="fa fa-meh-o fa-3x t'.$id.'" style="color: #ffed00;" data-index="1"></i>
                            <i data-ticket="'.$id.'" class="fa fa-smile-o fa-3x t'.$id.'" style="color: #00b400;" data-index="2"></i>                     
                        </div>
                    </div>
                    <div class="form-group">
                            <textarea class="form-control" id="comment'.$id.'" name="comment'.$id.'" placeholder="'.$lang['SURVEY_ENTER_COMMENT'].'"></textarea>
                            <div class="invisible" ><input type="text" name="'.$id.'" id="'.$id.'"></div>
                    </div><br>';
    }
    $bodySurvey.='<div class="form-footer text-center">
                    <button type="button" class="btn btn-default closeModal" data-dismiss="modal">'.$lang['BUTTON_CANCEL'].'</button>
                    <button type="submit" class="btn btn-primary">'.$lang['FRM_UPDATE'].'</button>
                </div>
                </form>';
    
    $tableSurvey ='<div class="toggle toggle-transparent toggle-bordered-full">'.$bodySurvey.'</div>';
    $tableSurvey =$bodySurvey;

    $response = new Response();
    $response->assign('contentModal', 'innerHTML', $tableSurvey);
    $response->script('$("#modal-title").html("'.$title.'");$("#modalp").modal({"show":true});');
    $response->script($scriptRating);
    return $response;

}

function displayTicket($idTicket){
    Global $lang;
    
    $freshTickets= new freshdesk();    
    $detail = $freshTickets->getTicketDetail($idTicket);
    
    $description = $detail['description'];
    $subjectTicket = $detail['subject'];
    
    $display = "<div id='contentDesc'></div>
        <table class='table table-sm table-striped'><tbody>
          <tr><th>$description</th></tr>";
    
    $freshTickets= new freshdesk();    
    $tab = $freshTickets->getTicket($idTicket);
    foreach ($tab as $key=>$values){
            
        $date = new DateTime($values['created_at']);
        $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
        $date_created = $date->format('Y-m-d H:i');
        
        $subject = $values['body'];
        $private = $values['private'];
        
        $displayAttachment = "";      
        if (count($values['attachments']) > 0){
            
            $displayAttachment = "<div class='mt-10 mb-10'>";
            
            foreach ($values['attachments'] as $tt){
                $name = $tt['name'];
                $attachment_url = $tt['attachment_url'];
                
                $displayAttachment .= "<a class='btn btn-sm btn-primary' href='$attachment_url' target='_blank' rel='nofollow'><i class='fa fa-paperclip'></i> $name</a>";
            }
            $displayAttachment .= "</div>";
        }
        
        if (!$private){
            $display .= "<tr>
              <td><small>$date_created</small>".$subject.$displayAttachment."</td>
            </tr>";
        }
    }
    $display .= "</tbody></table>";
    //<form method='post' action='createticket.php' id='form_ticket' name='form_ticket' novalidate='novalidate'>
    $display .= "<div>
        <div class='box-static  p-30'>
            <form id='form_ticketreply' name='form_ticketreply' onsubmit='return jaxon_replyTicket(jaxon.getFormValues(\"form_ticketreply\"))'>
            
                <div class='clearfix'>
                    <div class='form-group'>
                            <textarea class='form-control' id='bodyreply' name='bodyreply' placeholder='".$lang["REQUEST_ENTER_REPLY"]."'></textarea>
                    </div>
                    <div class='result'></div>
                </div>                
                <div class='invisible' >
                    <input type='text' name='ticket_id' id='ticket_id' value='".$idTicket."'>
                </div>
                <div class='form-footer text-center'>
                    <button type='submit' class='btn btn-primary btn-lg' >".$lang["REQUEST_REPLY_BUTTON"]."</button>
                </div>
            </form>
        </div>
    </div>";
    
    $response = new Response();
    $response->assign('contentModal', 'innerHTML', $display);
    //$response->assign('modal-title', 'innerHTML', $subjectTicket);
    $response->script('$("#modal-title").html("'.$subjectTicket.'");$("#modalp").modal({"show":true});');
    
    return $response;
}
function replyTicket($fields){
    Global $lang; Global $user;
    
    $user_id = $user->data()->fd_id;
    
    $response = new Response();
    
    //$bodyreply = strip_tags(trim($fields['bodyreply']));
    $bodyreply = $fields['bodyreply'];
    
    
    if(empty($bodyreply)){
        $errors[] = $lang['REQUEST_FILL_MSG'];
        $response->alert($lang['REQUEST_FILL_MSG']);
        return $response;
    }
    
    $ticket_id = intval($fields['ticket_id']);
    
    $content['user_id'] = intval($user_id);
    $content['body'] = $bodyreply;

    file_put_contents('log/log-reply-ticket_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [REQT] '. json_encode($content,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
    
    $freshUsers= new freshdesk(); 
    $respreply = $freshUsers->addTicketReply($ticket_id,$content);

    file_put_contents('log/log-reply-ticket_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [RESP] '. json_encode($respreply,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
    
   $response->assign('modalp', 'style.display', 'none');
   $response->redirect('index.php');
    
    return $response;
    
}
function displayProducts(){
    $freshObj= new freshdesk();    
    $tab = $freshObj->getProducts();

    $display = "<table class='table table-hover'>
              <thead><tr><th>name</th><th>id</th><th>description</th></tr></thead><tbody>";

    foreach ($tab as $values){
            $id=$values['id'];
            $name=$values['name'];
            $description=$values['id'];
            $display .= "<tr>
              <th scope='row'>$name</th>
              <td>$id</td>
              <td>$description</td>
            </tr>";
    }
    $display .= "</tbody></table>";
    $response = new Response();    
    $response->assign('contentProducts', 'innerHTML', $display);
    $response->script('window.fcWidget.open();');
    return $response;

}

function displayFolders(){
    $freshObj= new freshdesk();    
    $tab = $freshObj->getFolders($lang);
  
        $display = "<table class='table table-hover'>
                  <thead><tr><th>name</th><th>id</th><th>description</th></tr></thead><tbody>";

        $tab = $freshObj->getFolders('en');
                $id=$tab["id"];
                $name=$tab["name"];
                $description=$tab["description"];
                $display .= "<tr>
                  <th scope='row'>$id</th>
                  <td>$name</td>
                  <td>$description</td>
                </tr>";
 
       
        $display .= "</tbody></table>";
        
        $response = new Response();    
        $response->assign('contentFolders', 'innerHTML', $display);
        return $response;

}
function displayArticle(){
    $freshObj= new freshdesk();    
    
        $display = "<table class='table table-hover'>
                  <thead><tr><th>id</th><th>title</th><th>description/th><th>updated</th></tr></thead><tbody>";

        $tab = $freshObj->getArticle('en');
                $id=$tab["id"];
                $title=$tab["title"];
                $description=$tab["description"];
               $date=$tab["updated_at"];
                $display .= "<tr>
                  <th scope='row'>$id</th>
                  <td>$title</td>
                  <td>$description</td>
                  <td>$date</td>
                </tr>";
                
        $tab = $freshObj->getArticle('ja-JP');
                $id=$tab["id"];
                $title=$tab["title"];
                $description=$tab["description"];
                $date=$tab["updated_at"];
                $display .= "<tr>
                  <th scope='row'>$id</th>
                  <td>$title</td>
                  <td>$description</td>
                  <td>$date</td>
                </tr>";
       
        $display .= "</tbody></table>";
        
        $response = new Response();    
        $response->assign('contentFolders', 'innerHTML', $display);
        return $response;

}

function closeModal(){
     $response = new Response();    
    $response->assign('contentModal', 'innerHTML', '');
    $response->assign('modalp', 'style.display', 'none');
    return $response;
}

function updateLangagebyCompany($id_company,$page=1){
    $freshUsers= new freshdesk();    
    $tabUsers = $freshUsers->getUsersbyCompany($id_company,$page);
    $i=0;
    foreach ($tabUsers as $key=>$values){
        $id_user=$values['id'];
        $result = updatechinese($id_user);
        echo $id_user.';';
        $i++;
    }
    
    echo $i;
    return true;
}
function updateLanguageToJapanese($user_id){
    $tab['language'] = 'ja-JP';
    $fdUsers= new freshdesk();                        
    $values = $fdUsers->updateContact($user_id,$tab);
    return $values;
}
