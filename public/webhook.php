<?php
/*
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    header('Access-Control-Allow-Methods: GET, POST, PUT');
*/

require_once '../init.php';
$db = DB::getInstance();

error_reporting(E_ALL);
ini_set('display_errors', 1);

function insertSurvey($idUser,$idTicket,$type,$subject){
    global $db;
    // 2 = feature on hold
            $type = 1;
    // to be removed
            
    date_default_timezone_set('UTC');
    $date_utc = date("Y-m-d H:i:s");
    
    $sql="REPLACE INTO survey 
         ( id_ticket, id_contact, subject, type_survey, create_at) 
         VALUES ('$idTicket', '$idUser', '$subject', '$type', '$date_utc');";
    
    $query = $db->query($sql);
    return $sql;
}

header('Content-Type: application/json');
$input = file_get_contents('php://input');

$request= json_decode($input);
if (isset($request->webhook) && !empty($request->webhook)){
    $values = $request->webhook;

    file_put_contents('log/webhookrequest_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - '. json_encode($values,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
    
    if ($values->ticket_status == "Resolved"){

        $idUser = $values->requester_id;
        $idTicket = $values->ticket_id;
        $type = 1;
        $subject = $values->ticket_subject;

        $insert = insertSurvey($idUser,$idTicket,$type,$subject);
    }
}
?>

