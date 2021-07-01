<?php
use Jaxon\Jaxon;
use Jaxon\Response\Response;

require_once ('fct_misc_tools.php');
require_once ('class_freshdesk.php');
require_once ('class_events.php');
//Ajax Librairy ----
// and the Response class
// Get the core singleton object

$jaxon = jaxon(); // Get the core singleton object
$jaxon->register(Jaxon::USER_FUNCTION, 'displayEventsList');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayEventSchedule');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayEventAttendees');
$jaxon->register(Jaxon::USER_FUNCTION, 'addSchedule');
$jaxon->register(Jaxon::USER_FUNCTION, 'addAttendees');
$jaxon->register(Jaxon::USER_FUNCTION, 'addAttendees_prepend');
$jaxon->register(Jaxon::USER_FUNCTION, 'addEventDesc');
$jaxon->register(Jaxon::USER_FUNCTION, 'deleteAttendees');
$jaxon->register(Jaxon::USER_FUNCTION, 'deleteAttendeesbyID');
$jaxon->register(Jaxon::USER_FUNCTION, 'deleteSchedulebyID');
$jaxon->register(Jaxon::USER_FUNCTION, 'editFormSchedule');
$jaxon->register(Jaxon::USER_FUNCTION, 'editSchedulebyID');
$jaxon->register(Jaxon::USER_FUNCTION, 'checkEventAvailability');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayModalAddUser');
$jaxon->register(Jaxon::USER_FUNCTION, 'checkPayment');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayEventDescription');

function checkPayment($schedule_id,$client_reference_id,$payment_intent_id){
    Global $lang;Global $user;

    require 'stripe/config.php';
    $response = new Response();

    $user_id=$user->data()->fd_id;
    $codeLang = $_SESSION['lang']['code'];

    $scheduleDetail = getScheduleDetail($schedule_id);
    $detail = $scheduleDetail[0];

    $isAvailable  = checkEventAvailability($schedule_id,$user_id);
    
    if(!$isAvailable){
        $response->alert($lang['EVENT_AVAILABILITY']);
        //$response->alert($isAvailable);
        $response->redirect('event.php?u='.$detail->id_event);
        return $response;
    }

    \Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);
    $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
    $payment = $payment_intent->capture();

    if ($payment->status != "succeeded"){
        $response->alert($lang['STH_WENT_WRONG']);
        $response->redirect('event.php?u='.$detail->id_event);
        return $response;
    }
    
    $markup = "";
    $cf_category_level_1 = 'PENDING';
    $cf_category_level_event = 'Connect';
    $cf_sub_category = 'TPO Event';
    
    $descEvent = getEventDesc($detail->id_event);    
    if ($descEvent){
        $markup = ($codeLang == 'en' ? $descEvent->response_en : $descEvent->response_jp);
        $cf_category_level_1 = $descEvent->category_level_1;
        $cf_category_level_event = $descEvent->category_level_event;
        $cf_sub_category = $descEvent->sub_category;
    }
    
    $due_by = convert_JPtoGMT($detail->date_start."T".$detail->time_start.":00");
    
    $description = $detail->title . ": ". $detail->date_start ." ". $detail->time_start;
    $description .= '<br><br>' . $markup;
    
    $ticket = array(
                'subject' => $detail->title . ": ". $detail->date_start ." ". $detail->time_start,
                'description' => $description,
                'requester_id' => intval($user_id),
                'source' => 2,
                'status' => 2,
                'priority' => 1,
                'due_by' => $due_by,
		'cf_category_level_1' => $cf_category_level_1,
                'cf_category_level_event' => $cf_category_level_event,
		'cf_sub_category' => $cf_sub_category,
                'cf_travel_required' => 'No',
                'cf_type_of_request' => 'No',
                'cf_request_source' => 'Chatbot（オンライン）',
                'payment_intent' => $payment->id);
    
    $json_ticket = json_encode($ticket);
    $result = createAttendees($schedule_id,$user_id,1,$ticket);
    
    If ($result){
        $response->alert($lang['EVENT_SUBSCRIBE_OK']);
    } 
    else{
        $response->alert($lang['STH_WENT_WRONG']);
    }

    $response->assign('small-modal-content', 'innerHTML', '');
    $response->assign('myModal', 'style.display', 'none');
    
    $response->redirect('index.php#event');
    return $response;
}
function refundPayment($payment_intent_id){
    Global $company;
 
    $confStripe = $company['stripe'];

    \Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);
    $refund = \Stripe\Refund::create([
        'payment_intent' => $payment_intent_id,
      ]);

    return ($refund->status == "succeeded" ? TRUE : FALSE);

}
function addSchedule($valuesForm){
    $db = DB::getInstance();
    
    $id_event = $valuesForm['id_event'];
    $date_start = $valuesForm['date'];
    $date_end = $valuesForm['date'];
    $time_start = $valuesForm['start'];
    $time_end = $valuesForm['end'];
    $quantity = $valuesForm['qty'];
    $quantity_max = $valuesForm['max'];
                
    $sql="INSERT INTO event_schedule 
         (id_event, date_start, date_end, time_start, time_end, quantity, quantity_max) 
         VALUES ('$id_event', '$date_start', '$date_end', '$time_start', '$time_end', '$quantity', '$quantity_max');";
    
    $db->query($sql);
    
    $response = new Response();
    $script="jaxon_displayEventsList();jaxon_displayEventSchedule('$id_event');";
    $response->script($script);
    return $response;
}
function addEventDesc($valuesForm){
    
    $db = DB::getInstance();
    
    $id_event = $valuesForm['id_event_desc'];   
    $editordata_en = $valuesForm['editordata_en'];
    //$editordata_en = Input::sanitize($valuesForm['editordata_en']);
    $editordata_jp = $valuesForm['editordata_jp'];
    $cf_category_level_1 = $valuesForm['category_level_1'];
    $cf_category_level_event = $valuesForm['category_level_event'];
    $sub_category = $valuesForm['sub_category'];
    $amount = $valuesForm['amount'];
    
    $query = $db->insert("event",
                ['id_event'=>$id_event, 'response_en'=>$editordata_en,'response_jp'=>$editordata_jp,'category_level_1'=>$cf_category_level_1,'category_level_event'=>$cf_category_level_event,'sub_category'=>$sub_category,'amount'=>$amount],TRUE);
    
    $response = new Response();
    $script="jaxon_displayEventsList();jaxon_displayEventSchedule('$id_event');";
    
    $response->script($script);
    return $response;
}
function addAttendees_prepend($data){
    Global $user; Global $lang;
    $response = new Response();    
    
    $schedule_id = intval($data['schedule_id']);
    $user_id = intval($data['user_id']);
    $isAvailable  = checkEventAvailability($schedule_id);
    
    if(!$isAvailable){
        $response->alert($lang['EVENT_AVAILABILITY']);
        $response->redirect($_SERVER['REQUEST_URI']);
        return $response;
    }
    $scheduleDetail = getScheduleDetail($schedule_id);
    $detail = $scheduleDetail[0];
    
    $markup = "";
    $cf_category_level_1 = 'PENDING';
    $cf_category_level_event = 'Connect';
    $cf_sub_category = 'TPO Event';
    
    $descEvent = getEventDesc($detail->id_event);    
    if ($descEvent){
        $markup = ($codeLang == 'en' ? $descEvent->response_en : $descEvent->response_jp);
        $cf_category_level_1 = $descEvent->category_level_1;
        $cf_category_level_event = $descEvent->category_level_event;
        $cf_sub_category = $descEvent->sub_category;
    }
    
    $description = $detail->title . ": ". $detail->date_start ." ". $detail->time_start;
    $description .= '<br><br>' . $markup;
    
    $due_by = convert_JPtoGMT($detail->date_start."T".$detail->time_start.":00");
    $ticket = array(
                'subject' => $detail->title . ": ". $detail->date_start ." ". $detail->time_start,
                'description' => $description,
                'requester_id' => intval($user_id),
                'source' => 2,
                'status' => 2,
                'priority' => 1,
                'due_by' => $due_by,       
                'cf_category_level_1' => $cf_category_level_1,
                'cf_category_level_event' => $cf_category_level_event,
                'cf_sub_category' => $cf_sub_category,
                'cf_travel_required' => 'No',
                'cf_type_of_request' => 'No',
                'cf_request_source' => 'Chatbot（オンライン）');
    $json_ticket = json_encode($ticket);
    $result = createAttendees($schedule_id,$user_id,1,$ticket);
    
    If ($result){
        $response->assign('small-modal-content', 'innerHTML', '');
        $response->assign('myModal', 'style.display', 'none');
        $response->alert($lang['EVENT_SUBSCRIBE_OK']);        
    }
    $response->redirect('admin.php');
    return $response;
    
}
function addAttendees($data,$direct = FALSE){
    Global $user; Global $lang;
    $response = new Response();
    
    $user_id=$user->data()->fd_id;
    $codeLang = $_SESSION['lang']['code'];
    
    $schedule_id = intval($data['schedule_id']);

    $isAvailable  = checkEventAvailability($schedule_id);
    
    if(!$isAvailable){
        $response->alert($lang['EVENT_AVAILABILITY']);
        $response->redirect($_SERVER['REQUEST_URI']);
        return $response;
    }
    $scheduleDetail = getScheduleDetail($schedule_id);
    $detail = $scheduleDetail[0];
    
    $markup = "";
    $cf_category_level_1 = 'PENDING';
    $cf_category_level_event = 'Connect';
    $cf_sub_category = 'TPO Event';
    
    $descEvent = getEventDesc($detail->id_event);    
    if ($descEvent){
        $markup = ($codeLang == 'en' ? $descEvent->response_en : $descEvent->response_jp);
        $cf_category_level_1 = $descEvent->category_level_1;
        $cf_category_level_event = $descEvent->category_level_event;
        $cf_sub_category = $descEvent->sub_category;
    }
    
    $due_by = convert_JPtoGMT($detail->date_start."T".$detail->time_start.":00");
    
    $description = $detail->title . ": ". $detail->date_start ." ". $detail->time_start;
    $description .= '<br><br>' . $markup;
    
    $ticket = array(
                'subject' => $detail->title . ": ". $detail->date_start ." ". $detail->time_start,
                'description' => $description,
                'requester_id' => intval($user_id),
                'source' => 2,
                'status' => 2,
                'priority' => 1,
                'due_by' => $due_by,
		        'cf_category_level_1' => $cf_category_level_1,
                'cf_category_level_event' => $cf_category_level_event,
		        'cf_sub_category' => $cf_sub_category,
                'cf_travel_required' => 'No',
                'cf_type_of_request' => 'No',
                'cf_request_source' => 'Chatbot（オンライン）');
    
    $json_ticket = json_encode($ticket);

    if ($direct){
        if (!isScheduleBooked($schedule_id,$user_id)){
            $result = createAttendees($schedule_id,$user_id,1,$ticket); 
        }
        $submitButton = '<button type="button" class="btn btn-primary" data-dismiss="modal">'.$lang['MODAL_CLOSE'].'</button>';
        $response->assign('small-modalEvent-content', 'innerHTML', $description);
        $response->assign('modalEvent-footer', 'innerHTML', $submitButton);
        $response->script('$("#myModalEventLabel").html("'.$detail->title.'");$("#myModalEvent").modal({"show":true});');
        
        return $response;
    }
    else{
        $result = createAttendees($schedule_id,$user_id,1,$ticket);

        
        If ($result){
            $response->alert($lang['EVENT_SUBSCRIBE_OK']);
        } 
        else{
            $response->alert($lang['STH_WENT_WRONG']);
        }

        $response->assign('small-modal-content', 'innerHTML', '');
        $response->assign('myModal', 'style.display', 'none');
        
        $response->redirect('index.php#event');
        return $response;
    }
}
function createAttendees($schedule,$user,$qty,$data){  
    $db = DB::getInstance();
    
    $fields["requester_id"] = $data["requester_id"];
    $fields["subject"] = $data["subject"];
    $fields["description"] = $data["description"];
    $fields["source"] = $data["source"]; 
    $fields["status"] = $data["status"];
    $fields["priority"] = $data["priority"];
    $fields["due_by"] = $data["due_by"];
    $fields["fr_due_by"] = $data["due_by"];    
    //$customField["cf_category"] = $data["cf_category"];
    $customField["cf_category_level_1"] = $data["cf_category_level_1"];
    $customField["cf_category_level_event"] = $data["cf_category_level_event"];
    $customField["cf_sub_category"] = $data["cf_sub_category"];
    $customField["cf_travel_required"] = $data["cf_travel_required"];
    $customField["cf_type_of_request"] = $data["cf_type_of_request"];
    $customField["cf_request_source"] = $data["cf_request_source"];
    $fields['custom_fields'] = $customField;
    $log = $fields;
    $log["description"] = substr($log["description"],0,50);

    $payment_intent = (isset($data["payment_intent"]) ? $data["payment_intent"] : NULL);

    file_put_contents('log/log-ticket_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [REQT:] '. json_encode($log,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
    
    $freshUsers= new freshdesk();    
    $newTicket = $freshUsers->addTicket($fields);
    
    file_put_contents('log/log-ticket_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [RESP] '. json_encode($newTicket,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
        
    if (isset($newTicket['id']) && !empty($newTicket['id'])){
        date_default_timezone_set("UTC");
        $fd_ticket_id = $newTicket['id'];
        $created_at = date("Y-m-d H:i:s");
        
        //sendmail('moreno@bineex.com','!! Ticket created !!',json_encode($newTicket));
        
        $sql="INSERT INTO event_attendees 
         (id_event_schedule, id_user, quantity, id_ticket, created_at, payment_intent ) 
         VALUES ('$schedule', '$user', '$qty','$fd_ticket_id', '$created_at', '$payment_intent')";
    
        $db->query($sql);
        return $fd_ticket_id;
    }
    else {
        return FALSE;
    }
}
function deleteAttendees($schedule,$user){
    Global $lang;
    $db = DB::getInstance();
    $sql="SELECT id_event_attds, id_ticket, payment_intent FROM event_attendees where deleted_at is null and id_event_schedule = $schedule and id_user = $user";   
    $db->query($sql);
    
    $result = $db->results();        
    $ticket = $result[0];
    $id_ticket = $ticket->id_ticket;
    $id_event_attds = $ticket->id_event_attds;
    $payment_intent = $ticket->payment_intent;
    
    
    $freshUsers= new freshdesk();
    //Add a private note Ticket
    $note["body"] = "Cancelled by the user";
    $responseTicket = $freshUsers->addNoteTicket($id_ticket,$note);    
    
    //Close Ticket
    $fields["status"] = 5; //5 => closed    
    $responseTicket = $freshUsers->closeTicket($id_ticket,$fields);
    
    $sql="UPDATE event_attendees SET deleted_at = now(), deleted_by ='$user'
            where id_event_attds = $id_event_attds";    
    $db->query($sql);

    if(!empty($payment_intent)){
        $refund = refundPayment($payment_intent);
    }
    
    $response = new Response();
    $response->assign('small-modal-content', 'innerHTML', '');
    $response->assign('myModal', 'style.display', 'none');
    $response->alert($lang['EVENT_UNSUBSCRIBE_OK']);
    $response->redirect('index.php#event');
    return $response;
}
function deleteAttendeesbyID($id_row){
    Global $lang; Global $user;
    
    $uid = $user->data()->fd_id;
    
    $db = DB::getInstance();
    $sql="SELECT id_event_attds, id_ticket from event_attendees where id_event_attds = $id_row ";   
    $db->query($sql);
    
    $result = $db->results();        
    $ticket = $result[0];
    $id_ticket = $ticket->id_ticket;
    
    $fields["status"] = 5; //5 => closed
    
    $freshUsers= new freshdesk();
    $responseTicket = $freshUsers->closeTicket($id_ticket,$fields);
    
    $sql="UPDATE event_attendees SET deleted_at = now(), deleted_by ='$uid'
            where id_event_attds = $id_row";    
    $db->query($sql);

    
    $response = new Response();
    $response->alert($lang['EVENT_UNSUBSCRIBE_OK']);
    $response->redirect('admin.php');
    return $response;
}
function deleteSchedulebyID($id_schedule,$id_event = null){
    Global $lang; Global $user;
        
    $response = new Response();
    
    $db = DB::getInstance();
    $sql="delete from event_schedule where id_event_schedule = $id_schedule ";
    $db->query($sql);
    
       
     $response = new Response();
    $script="jaxon_displayEventsList();jaxon_displayEventSchedule('$id_event');";
    $response->script($script);
    return $response;
}
function editFormSchedule($id_schedule){
    Global $lang;
            
    $db = DB::getInstance();
    $sql="select id_event,id_event_schedule,date_start,TIME_FORMAT(time_start,'%H:%i') as time_start,TIME_FORMAT(time_end,'%H:%i') as time_end,quantity,quantity_max
         from event_schedule where id_event_schedule = $id_schedule ";
    $query = $db->query($sql);
    $result = $query->first();
    
    $script ="$('#date').val('".$result->date_start."');
            $('#start').val('".$result->time_start."');
            $('#end').val('".$result->time_end."');
            $('#qty').val('".$result->quantity."');
            $('#max').val('".$result->quantity_max."');
            $('#id_event').val('".$result->id_event."');
            $('#id_event_schedule').val('".$result->id_event_schedule."');
            $('#schedulebtn').html('Edit schedule');
            $('#myform').attr('onsubmit', 'return jaxon_editSchedulebyID(jaxon.getFormValues(\'myform\'))');";
        
    $response = new Response();
    $response->script($script);
    return $response;
}
function editSchedulebyID($valuesForm){
    $db = DB::getInstance();
    
    $id_event = $valuesForm['id_event'];
    $id_event_schedule = $valuesForm['id_event_schedule'];
    $date_start = $valuesForm['date'];
    $date_end = $valuesForm['date'];
    $time_start = $valuesForm['start'];
    $time_end = $valuesForm['end'];
    $quantity = $valuesForm['qty'];
    $quantity_max = $valuesForm['max'];
                
    $sql="UPDATE event_schedule set
         date_start = '$date_start',
         date_end = '$date_end',
         time_start ='$time_start',
         time_end = '$time_end',
         quantity = '$quantity',
         quantity_max = '$quantity_max'
          WHERE id_event_schedule = $id_event_schedule";    
    $db->query($sql);
    
    $script ="$('#date').val('');
            $('#start').val('');
            $('#end').val('');
            $('#qty').val('');
            $('#max').val('');
            $('#id_event').val('');
            $('#id_event_schedule').val('');
            $('#schedulebtn').html('Add schedule');
            $('#myform').attr('onsubmit', 'return jaxon_addSchedule(jaxon.getFormValues(\'myform\'))');";
   
    $script="jaxon_displayEventsList();jaxon_displayEventSchedule('$id_event');";
    $response = new Response();
    $response->script($script);
    return $response;
}
function getScheduleDetailImg($id,$code_lang = NULL){
    $db = DB::getInstance();
     if ($code_lang == NULL){$code_lang = $_SESSION['lang']['code'];}
     
     $sql="SELECT s.id_event,s.id_event_schedule,a.title,s.date_start,TIME_FORMAT(time_start,'%H:%i') as time_start,TIME_FORMAT(time_end,'%H:%i') as time_end, fd_articles_img.img_url,amount
         FROM event_schedule s
         JOIN fd_articles a on s.id_event = a.article_id and language = '$code_lang'
         left join fd_articles_img on a.article_id = fd_articles_img.article_id
         left join event on a.article_id = event.id_event
         WHERE s.id_event_schedule = $id";

     $db->query($sql);
     $r = $db->results();
     return $r;
}
function getScheduleDetail($id,$code_lang = NULL){
       $db = DB::getInstance();
        if ($code_lang == NULL){$code_lang = $_SESSION['lang']['code'];}
        
        $sql="SELECT s.id_event,s.id_event_schedule,a.title,s.date_start,TIME_FORMAT(time_start,'%H:%i') as time_start,TIME_FORMAT(time_end,'%H:%i') as time_end
            FROM event_schedule s
            JOIN fd_articles a on s.id_event = a.article_id and language = '$code_lang'
            WHERE s.id_event_schedule = $id";

        $db->query($sql);
        $r = $db->results();
        return $r;
}
function getListSchedulesBooked($event_id,$user_id){
    $db = DB::getInstance();
        
    $sql="select s.id_event_schedule
        from event_schedule s 
        join event_attendees a on s.id_event_schedule = a.id_event_schedule
        where id_event=$event_id and a.id_user = $user_id and deleted_at is null
        group by s.id_event_schedule";

        $db->query($sql);
        $r = $db->results();
        $tab = array();
        
        foreach ($r as $key => $value) {
            $tab[$key] = $value->id_event_schedule;
        }
        return $tab;
}
function getListSchedules($event_id){
       $db = DB::getInstance();
        
        $sql="select s.id_event_schedule, id_event,date_start,date_end,TIME_FORMAT(time_start,'%H:%i') as time_start,TIME_FORMAT(time_end,'%H:%i') as time_end,s.quantity as qty_total,SUM(IF(a.quantity is null,0,a.quantity)) as qty_booked 
            from event_schedule s 
            left join event_attendees a on s.id_event_schedule = a.id_event_schedule and deleted_at is null
            where id_event=$event_id
            group by s.id_event_schedule 
            order by date_start desc, time_start";
            $db->query($sql);
            $r = $db->results();
            return $r;
}
function getEventTitle($event_id){
    $db = DB::getInstance();
    $sql="SELECT article_id,title,language FROM fd_articles           
           where article_id = $event_id ";
    $db->query($sql);
    $rows = $db->results();
    
    foreach($rows as $value){
        $event['title'][$value->language] = $value->title;
    }
    
    if (isset($event['title']['ja-JP']) || !empty($event['title']['ja-JP'])){
            $title = $event['title']['ja-JP'];
    } else {
        $title = $event['title']['en'];
    }

    return $title;
}
function getListAttendees($schedule_id){
       $db = DB::getInstance();
        
        $sql="SELECT a.id_event_attds,u.fd_id,a.quantity,date(a.created_at) as created_at,a.deleted_at,u.email,u.fname,u.lname
              FROM event_attendees a 
              JOIN users u on a.id_user = u.fd_id 
              WHERE a.id_event_schedule = $schedule_id and a.deleted_at is null";


            $db->query($sql);
            $r = $db->results();
            return $r;
}
function checkEventAvailability($schedule_id,$user_id = NULL){
       $db = DB::getInstance();
        
        $sql="select s.id_event_schedule, id_event, s.quantity, s.quantity_max, SUM(IF(a.quantity is null,0,a.quantity)) as qty_booked 
            from event_schedule s 
            left join event_attendees a on s.id_event_schedule = a.id_event_schedule and deleted_at is null
            WHERE s.id_event_schedule = $schedule_id
            group by s.id_event_schedule";
        
            $db->query($sql);
            $result = $db->results();
            $row = $result[0];
            
            $qty_available = $row->quantity - $row->qty_booked;     
            $isAvailable = ($qty_available < 1 ? FALSE : TRUE );
            
            if ($isAvailable && $user_id != NULL){
                $sql="SELECT count(*) nb FROM event_attendees where id_event_schedule = $schedule_id and id_user = $user_id and deleted_at is null";
                $db->query($sql);
                $result = $db->results();
                $row = $result[0];
                $isAvailable = ($row->nb < 1 ? TRUE : FALSE );
            }
            
        return $isAvailable;
}
function getEventDesc($event_id){
    $db = DB::getInstance();
    $sql = "SELECT * FROM event where id_event = ".$event_id;
          
    $query = $db->query($sql);
   return ($query->count() > 0  ?  $query->first()  :  FALSE);
    
}
function displayEventSchedule($id_event){
    Global $scriptJQueryLoad;
    $Event = getListSchedules($id_event);
    $title = getEventTitle($id_event);
    
    $form_schedule='<form class="form-inline" id="myform" onsubmit="return jaxon_addSchedule(jaxon.getFormValues(\'myform\'))">

                <label class="sr-only" for="date">Date</label>
                <input type="text" class="form-control form-control-sm mr-2 datepicker" data-format="yyyy-mm-dd" data-lang="en" data-RTL="false" id="date" name="date"  placeholder="Date" required>                
                
                    
                <label class="sr-only" for="start">Start</label>
                <input type="text" class="form-control form-control-sm mr-2 masked" data-format="99:99" data-placeholder="_" id="start" name="start" placeholder="Time start" required>
                
                
                <label class="sr-only" for="end">End</label>
                <input type="text" class="form-control form-control-sm mr-2 masked" data-format="99:99" data-placeholder="_" id="end" name="end" placeholder="Time end" required>
                

                <label class="sr-only" for="qty">Qty</label>
                <input type="text" class="form-control form-control-sm mr-2" id="qty" name="qty"  placeholder="Quantity" required>

                <label class="sr-only" for="max">Max</label>
                <input type="text" class="form-control form-control-sm mr-2" id="max" name="max" placeholder="Qty max" required>

                <button type="submit" id="schedulebtn" name="schedulebtn" class="btn btn-primary mb-2">Add schedule</button>
                <input type="text" class="invisible" id="id_event" name="id_event" value="'.$id_event.'">
                <input type="text" class="invisible" id="id_event_schedule" name="id_event_schedule" value="">
                
        </form>';
    
    $form_desc ="<div id='summernote_jp'></div>
        <div id='summernote_en'></div>
        <form class='form-group' id='myformEvent' enctype='multipart/form-data' onsubmit='return (get_editordata() && jaxon_addEventDesc(jaxon.getFormValues(\"myformEvent\")));'>

                    <div class='form-row'>
                        <div class='form-group col-md-3'>
                          <label for='category_level_1'>Category level 1</label>
                          <select class='form-control' id='category_level_1' name='category_level_1'>
                              <option value='子供 / Child'>子供 / Child</option>
                              <option value='家族、夫婦、パートナー / Partner/ Spouse/Family'>家族、夫婦、パートナー / Partner/ Spouse/Family</option>
                              <option value='親・親戚 / Extended family'>親・親戚 / Extended family</option>
                              <option value='友人 / Friends / Social'>友人 / Friends / Social</option>
                              <option value='自分 / Personal'>自分 / Personal</option>
                              <option value='同僚・職場 / Collegues'>同僚・職場 / Collegues</option>
                              <option value='その他 / Others'>その他 / Others</option>
                              <option value='PENDING'>PENDING</option>
                              <option value='MERGED'>MERGED</option>
                            </select>
                        </div>
                        <div class='form-group col-md-3'>
                          <label for='cf_sub_category'>Category Level 2</label>
                          <select class='form-control' id='sub_category' name='sub_category'>
                              <option value='（子供）習い事・学童 / Learning - extra curricular - tennis etc school exams'>（子供）習い事・学童 / Learning - extra curricular - tennis etc school exams</option>
                              <option value='ベビーシッター / Nanny'>ベビーシッター / Nanny</option>
                              <option value='お出かけ（休日、日帰り） / Activities ( week-end, day trip etc)'>お出かけ（休日、日帰り） / Activities ( week-end, day trip etc)</option>
                              <option value='幼保・学校・留学 / Local School /Nursery/Oversea Studies'>幼保・学校・留学 / Local School /Nursery/Oversea Studies</option>
                              <option value='人間関係/健康 / Relationship/ Healthcare (physical mental)'>人間関係/健康 / Relationship/ Healthcare (physical mental)</option>
                              <option value='ヘルスケア（病院） / Healthcare (hospital)'>ヘルスケア（病院） / Healthcare (hospital)</option>
                              <option value='食事会・集まり / Gathering / Restaurant'>食事会・集まり / Gathering / Restaurant</option>
                              <option value='旅行（海外） / Travel(overseas)'>旅行（海外） / Travel(overseas)</option>
                              <option value='旅行（国内） / Travel(domestic)'>旅行（国内） / Travel(domestic)</option>
                              <option value='（大人）学び/趣味 / Hobbies Learning'>（大人）学び/趣味 / Hobbies Learning</option>
                              <option value='ギフト / Gift'>ギフト / Gift</option>
                              <option value='家・生活関連 / House works, Reform'>家・生活関連 / House works, Reform</option>
                              <option value='資産形成 / Finance, Insurance'>資産形成 / Finance, Insurance</option>
                              <option value='家事代行 / Housekeeping'>家事代行 / Housekeeping</option>
                              <option value='介護 / Elderly care'>介護 / Elderly care</option>
                              <option value='手続きの相談 / Admin'>手続きの相談 / Admin</option>
                              <option value='冠婚葬祭・ライフイベント / LIfe events'>冠婚葬祭・ライフイベント / LIfe events</option>
                              <option value='予約代行 / Booking'>予約代行 / Booking</option>
                              <option value='買物代行 / Shopping'>買物代行 / Shopping</option>
                              <option value='業務：接待、手土産等 / Biz-Gathering, Gift,etc'>業務：接待、手土産等 / Biz-Gathering, Gift,etc</option>
                              <option value='業務：その他 / Biz-Other'>業務：その他 / Biz-Other</option>
                              <option value='TPO Event'>TPO Event</option>
                              <option value='その他 / Other'>その他 / Other</option>
                              <option value='PENDING'>PENDING</option>
                            </select>
                        </div>
                        <div class='form-group col-md-4'>
                          <label for='category_level_event'>Category Level Event</label>
                          <select class='form-control' id='category_level_event' name='category_level_event'>                              
                              <option value='Move'>Move</option>
                              <option value='Learn'>Learn</option>
                              <option value='Family'>Family</option>
                              <option value='Create'>Create</option>
                              <option value='Self-care'>Self-care</option>
                              <option value='Connect'>Connect</option>
                            </select>
                        </div>
                        <div class='form-group col-md-2'>
                          <label for='amount'>Amount</label>
                          <input type='text' class='form-control form-control-sm mr-2' id='amount' name='amount' value='0'>
                        </div>
                    </div>
                    <button type='submit' class='btn btn-primary btn-lg-xs'>Edit</button>
                    <div class='invisible' >
                        <textarea id='editordata_jp' name='editordata_jp' rows='1'></textarea>
                        <textarea id='editordata_en' name='editordata_en' rows='1'></textarea>
                        <input type='text' class='invisible' id='id_event_desc' name='id_event_desc' value='".$id_event."'>
                    </div>
                    
                </form>";
    
    $table = "<table class='table table-sm table-hover' id='datatable_sample' style='width:100%'>
                <thead>
                    <th>Date</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Qty</th>
                    <th>Booked</th>
                    <th></th>
                </thead>
                <tbody>";

                foreach ($Event as $value) {
                    $id=$value->id_event_schedule;        
                    $date_start=$value->date_start;
                    $date_end=$value->date_end;
                    $time_start=$value->time_start;
                    $time_end=$value->time_end;
                    $total=$value->qty_total;
                    $booked=$value->qty_booked;
                    $titleS = $title."<br> ".$date_start.": ".$time_start." - ".$time_end;
                    
                    $table .= "<tr class='odd gradeX'>
                            <td onclick='jaxon_displayEventAttendees(\"".$id."\",\"".$titleS."\");return false;'><h4 class='m-0 fs-16'>".$date_start."</h4></td>
                            <td>".$time_start."</td>
                            <td>".$time_end."</td>
                            <td>".$total."</td>
                            <td>".$booked."</td>
                           <td>
                                <a href='#' onclick='jaxon_editFormSchedule(".$id."); return false;' class='btn btn-default btn-sm'><i class='fa fa-edit white'></i> Edit </a>";
                        if($booked == 0){
                            $table .= "<a href='#' onclick='var answer = confirm (\"Delete ".$date_start.": ".$time_start." - ".$time_end." ?\");if (answer){ jaxon_deleteSchedulebyID(".$id.",".$id_event."); return false;}' class='btn btn-default btn-sm'><i class='fa fa-times white'></i> Delete </a>";
                        }
                      $table .= "</td>
                           
                    </tr>"; 
                }
    $table .= "</tbody>
            </table>";
    //$('#summernote_en').val('".$markup_en."');
    $descEvent = getEventDesc($id_event);
    if ($descEvent){
        $body = $form_desc.'<br />'.$form_schedule.'<br />'.$table;
        $markup_en = $descEvent->response_en;
        $markup_jp = $descEvent->response_jp;
        $sct = "$(document).ready(function() {            
            $('#category_level_1').val('".$descEvent->category_level_1."');
            $('#sub_category').val('".$descEvent->sub_category."');
            $('#category_level_event').val('".$descEvent->category_level_event."');
            $('#amount').val('".$descEvent->amount."');
            $('#summernote_jp').summernote('code','".$markup_jp."');
            $('#summernote_en').summernote('code','".$markup_en."');
          });";
    } else {
        $body = $form_desc.'<br />'.$form_schedule.'<br />'.$table;
        $sct = "$(document).ready(function() {
            $('#sub_category').val('TPO Event');
            $('#summernote_jp').summernote();
            $('#summernote_en').summernote(); 
          });";
    }
    $card='<div class="card">
                <div class="card-header">'.$title.'</div>
                <div class="card-body">
                    '.$body.'
                </div>
            </div>';    
    
    $response = new Response();   
    $response->assign('event_detail', 'innerHTML', $card);
    $response->script($sct);
    return $response;
}
function displayEventAttendees($id,$title){
    $List = getListAttendees($id);

    $display="<div class='card card-default'>
        <div class='card-heading'>
            <h5>$title</h5>
            <input type='button' class='btn btn-primary btn-lg-xs' href='#' onclick='jaxon_displayModalAddUser($id); return false;' value='Add' />
                
        </div>
        
        <table class='table table-striped table-bordered table-hover table-sm' id='datatable_sample'>
            <thead>
                <th>Attendee</th>
                <th>Mail</th>
                <th>Booking date</th>
                <th>Qty</th>
            </thead>

            <tbody>";
    
    foreach ($List as $value) {
        $id_event_attds=$value->id_event_attds;
        $fd_id=$value->fd_id;
        $fname=$value->fname;
        $lname=$value->lname;
        $quantity=$value->quantity;
        $created_at=$value->created_at;
        $email=$value->email;
        $fd_link="https://tpoconcierge.freshdesk.com/a/contacts/".$fd_id;
        
        $display .= "<tr class='odd gradeX'>
                <td>
                    <h4 class='m-0 fs-16'><a href='".$fd_link."' target='_blank'>".$lname." ".$fname."</a></h4>
                    <ul class='list-inline categories m-0 text-muted fs-11 hidden-xs-down'>
                            <li><a href='#' onclick='jaxon_deleteAttendeesbyID(".$id_event_attds."); return false;' class='text-danger'>unsubscribe</a></li>
                    </ul>
                </td>
                <td>".$email."</td>
                <td>".$created_at."</td>
                <td>".$quantity."</td>
        </tr>";
    }

    $display .= "</tbody>
    </table>
    </div>";
    
    $response = new Response();
    $response->assign('attendees_detail', 'innerHTML', $display);
     
    return $response;
}
function displayModalAddUser($schedule_id){
    Global $lang; Global $scriptJQueryLoad;
    
     $submitButton = "<button type='button' class='btn btn-default' data-dismiss='modal'>".$lang['BUTTON_CANCEL']."</button>
        <button type='submit' class='btn btn-secondary' >".$lang['BUTTON_SUBSCRIBE']."</button>";  
    //<button type='button' class='btn btn-secondary' onclick='jaxon_addAttendees_prepend(jaxon.getFormValues(\"form_account\"));return false;'>".$lang['BUTTON_SUBSCRIBE']."</button>";  
     
    $response = new Response();
    $response->assign('schedule_id', 'value', $schedule_id);
    $response->assign('user_id', 'value', '');
    $response->assign('user_input', 'value', '');
    $response->assign('modal-footer', 'innerHTML', $submitButton);
    $response->script('$("#myModalLabel").html("Add User");$("#myModal").modal("show");$( ".userpicker" ).autocomplete( "option", "appendTo", ".modal" );');
    
    return $response;
}
function displayEventsList(){
    Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    
    $Articles= new events();    
    $Eventlist = $Articles->getFullListEvents($codeLang);
    $event = array();
    foreach($Eventlist as $value){
        $event[$value->article_id]['title'][$value->language] = $value->title;
        $event[$value->article_id]['folder'] =$value->name;
        $event[$value->article_id]['date_start'] =$value->date_start;
        $event[$value->article_id]['qty_total'] =$value->qty_total;
        $event[$value->article_id]['qty_booked'] =$value->qty_booked;
    }
    
    $display = "
        <table class='table table-sm table-hover' id='datatable_sample'>
            <thead>
                <tr>                        
                    <th>Folder</th>
                    <th>Event</th>
                    <th>Date Start</th>
                    <th>Qty</th>
                    <th>Qty booked</th>
            </thead>

            <tbody>";
    
    foreach ($event as $id => $value) {
        
        $folder = $value['folder'];        
        $start = $value['date_start'];
        $total = $value['qty_total'];
        $attendees = $value['qty_booked'];
        
        If (isset($value['title']['ja-JP']) || !empty($value['title']['ja-JP'])){
            $title = $value['title']['ja-JP'];
        } else {
            $title = $value['title']['en'];
        }
        
        $display .= "<tr class='odd gradeX' onclick='jaxon_displayEventSchedule(\"".$id."\");return false;'>
                <td>".$folder."</td>
                <td>".$title."</td>
                <td>".$start."</td>
                <td>".$total."</td>
                <td>".$attendees."</td>

        </tr>";
    }

    $display .= "</tbody>
    </table>";
    
    $response = new Response();   
    $response->assign('event_list', 'innerHTML', $display);
    $response->script('$("#datatable_sample").DataTable();');
    return $response;
 
}
function displayModalAddSchedule(){
    Global $lang;
    $display='<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">				
				<h4 class="modal-title" id="myModalLabel">Schedule</h4>
			</div>

			<!-- Modal Body -->
			<div class="modal-body">
                            <div id="small-modal-content">
                                <div class="booking-form-highlight mb-30">
                                    <form class="form-inline" id="myform" onsubmit="return jaxon_addSchedule(jaxon.getFormValues(\'myform\'))">

                                        <label class="sr-only" for="date">Date</label>
                                        <input type="text" class="form-control masked mb-2 mr-sm-2" id="date" name="date" data-format="99-99-9999" data-placeholder="_" placeholder="Date" required>                

                                        <label class="sr-only" for="start">Start</label>
                                        <input type="text" class="form-control mb-2 mr-sm-2 masked" id="start" name="start" placeholder="Time start" required>

                                        <label class="sr-only" for="end">End</label>
                                        <input type="text" class="form-control mb-2 mr-sm-2 masked" id="end" name="end" placeholder="Time end" required>

                                        <label class="sr-only" for="qty">Qty</label>
                                        <input type="text" class="form-control mb-2 mr-sm-2" id="qty" name="qty"  placeholder="Quantity" required>

                                        <label class="sr-only" for="max">Max</label>
                                        <input type="text" class="form-control mb-2 mr-sm-2" id="max" name="max" placeholder="Qty max" required>
                                       
                                        <input type="text" class="invisible" id="id_event" name="id_event" >

                                    </form>
                                </div>
                            </div>
			</div>

			<!-- Modal Footer -->
			<div class="modal-footer" id="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">'.$lang["BUTTON_CANCEL"].'</button>
                            <button type="button" class="btn btn-primary" onclick="jaxon_addAttendees();return false;">ADD</button>
			</div>
		</div>
	</div>
</div>';
    return $display;
}
function isScheduleBooked($id_schedule,$user_id){
    $db = DB::getInstance();
    $sql = "SELECT * FROM event_attendees where id_event_schedule = $id_schedule and id_user = $user_id and deleted_at is null";
    
    $result = $db->query($sql);
    if($result->count() > 0){
        return TRUE;
    }
    return FALSE;
}
function displayEventDescription($id_event){
    Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $response = new Response();
    $description = "";
    $descEvent = getEventDesc($id_event);
    if ($descEvent){
        $description = ($codeLang == 'en' ? $descEvent->response_en : $descEvent->response_jp);
    }
    $submitButton = '<button type="button" class="btn btn-primary" data-dismiss="modal">'.$lang['MODAL_CLOSE'].'</button>';
    $response->assign('small-modalEvent-content', 'innerHTML', $description);
    $response->assign('modalEvent-footer', 'innerHTML', $submitButton);
    $response->script('$("#myModalEventLabel").html("");$("#myModalEvent").modal({"show":true});');
    
    return $response;
}
