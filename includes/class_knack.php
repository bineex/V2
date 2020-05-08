<?php
require '../vendor/autoload.php';
require_once '../init.php';
$db = DB::getInstance();

error_reporting(E_ALL);
ini_set('display_errors', 1);

use GuzzleHttp\Client as Guzzle;
//use GuzzleHttp\Psr7\Response as GuzzleResponse;

class knackapi {
    /**
     * @var string
     */
    protected $application_id = "5bf241d58a5344086aaa65d4";
    /**
     * @var string
     */
    protected $api_key = "eebcef70-ec6d-11e8-8c5e-1d83a427da1a";

    protected $guzzle;

    function postCompany($company_name){
        
        $url='https://api.knack.com/v1/objects/object_54/records';
        $client = new GuzzleHttp\Client();

        $application_id= $this->application_id;
        $api_key = $this->api_key;
        $method="POST";

        try{
            $response = $client->request($method, $url,[
                        'headers' => [
                            'X-Knack-Application-Id' => $application_id,
                            'X-Knack-REST-API-Key'   => $api_key,
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
}
