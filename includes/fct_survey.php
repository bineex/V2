<?php
$db = DB::getInstance();

require_once ('class_freshdesk.php');

function getListSurveyPending(){
        Global $db; Global $user;
        $fd_id = $user->data()->fd_id;
        
        $sql="SELECT * FROM survey where id_contact = $fd_id and type_survey = 1 and displayed_at is null"; 
        $db->query($sql);
        return $db->results(TRUE);
}
    
function flagSurveyDisplayed($id_ticket,$value = NULL){
        Global $db;
        date_default_timezone_set('UTC');        
        $date_utc = date("Y-m-d H:i:s");
        
        $sql="UPDATE survey SET displayed_at = '$date_utc', value = $value where id_ticket = $id_ticket  ";

        $db->query($sql);
        return $sql;
    }
function addSatisfactionRating($ticket_id,$rating_value){
    
    $content['feedback'] = 'Hello';
    $ratings['default_question'] = $rating_value;
    $ratings['question_35000063212'] = $rating_value;
    $ratings['question_35000063213'] = $rating_value;
    $content['ratings']=$rating;
    
    $freshUsers= new freshdesk();    
    $newRating = $freshUsers->postStatisfactionRating($ticket_id,$content);
    
    return $newRating;
}

