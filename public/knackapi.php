<?php
/*
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    header('Access-Control-Allow-Methods: GET, POST, PUT');
*/
require '../vendor/autoload.php';
require_once '../init.php';

require_once("../includes/class_knack.php");

$db = DB::getInstance();

error_reporting(E_ALL);
ini_set('display_errors', 1);

use GuzzleHttp\Client as Guzzle;

function postGuest(){
    $url='https://api.knack.com/v1/objects/object_53/records';
    $client = new GuzzleHttp\Client();

    $method="POST";
    try{
        $response = $client->request($method, $url,[
                    'headers' => [
                        'X-Knack-Application-Id' => '5bf241d58a5344086aaa65d4',
                        'X-Knack-REST-API-Key'   => 'eebcef70-ec6d-11e8-8c5e-1d83a427da1a',
                        'Content-Type'           => 'application/json'
                    ],
                    'body' => '{
                                "field_544": 2,
                                "field_536": {
                                  "first":"Jason",
                                  "last":"Bourne"
                                },
                                "field_537": "Jason Bourne",
                                "field_538": ["5db281410a7abe0015a8d3e4"],
                                "field_540": "Department",
                                "field_541": "Title",
                                "field_540": "Department"
                              }'
                ]);
        $body = $response->getBody();
        $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
        //$tab=json_decode($body, true);
        return $tab;
    } catch(Exception $e){
        return $e->getMessage();
    }

}
function postCompany($company_name){
    $url='https://api.knack.com/v1/objects/object_54/records';
    $client = new GuzzleHttp\Client();

    $method="POST";
    try{
        $response = $client->request($method, $url,[
                    'headers' => [
                        'X-Knack-Application-Id' => '5bf241d58a5344086aaa65d4',
                        'X-Knack-REST-API-Key'   => 'eebcef70-ec6d-11e8-8c5e-1d83a427da1a',
                        'Content-Type'           => 'application/json'
                    ],
                    'body' => '{
                                "field_546": "'.$company_name.'",
                                "field_547": "'.$company_name.'"
                              }'
                ]);
        $body = $response->getBody();
        $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
        //$tab=json_decode($body, true);
        return $tab;
    } catch(Exception $e){
        return $e->getMessage();
    }

}
function getGuestCompany(){
    $url='https://api.knack.com/v1/objects/object_46/records';
    $client = new GuzzleHttp\Client();

    $method="GET";
    try{
        $response = $client->request($method, $url,[
                    'headers' => [
                        'X-Knack-Application-Id' => '5bf241d58a5344086aaa65d4',
                        'X-Knack-REST-API-Key'   => 'eebcef70-ec6d-11e8-8c5e-1d83a427da1a',
                        'Content-Type'           => 'application/json'
                    ],                    
                ]);
        $body = $response->getBody();
        $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
        //$tab=json_decode($body, true);
        return $tab;
    } catch(Exception $e){
        return $e->getMessage();
    }

}
function getGuestList(){
    $url='https://api.knack.com/v1/objects/object_53/records';
    $client = new GuzzleHttp\Client();

    $method="GET";
    try{
        $response = $client->request($method, $url,[
                    'headers' => [
                        'X-Knack-Application-Id' => '5bf241d58a5344086aaa65d4',
                        'X-Knack-REST-API-Key'   => 'eebcef70-ec6d-11e8-8c5e-1d83a427da1a',
                        'Content-Type'           => 'application/json'
                    ],                    
                ]);
        $body = $response->getBody();
        $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
        //$tab=json_decode($body, true);
        return $tab;
    } catch(Exception $e){
        return $e->getMessage();
    }

}

$knackobj = new knackapi();
$result = $knackobj->postCompany('Fornetix');
print_r($result);
?>

