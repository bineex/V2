<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class_freshdesk
 *
 * @author mmo
 */
use GuzzleHttp\Client as Guzzle;
//use GuzzleHttp\Psr7\Response as GuzzleResponse;

class freshdesk {
    /**
     * @var string
     */
    //protected $Token = "JKn4zB6S6ZdxNhCEelXI";
    protected $Token = "QfR3Kgc6omH5ZNytYI8p";
    /**
     * @var string
     */
    protected $password = "x";

    /**
     * @var string
     */
    protected $freshdeskDomain;

    /**
     * @var Guzzle
     */
    protected $guzzle;

    /**
     * @var string[]
     */
    protected $endpoints = [
        'tickets' => '/helpdesk/tickets.json'
    ];


    /**
     * Construct class.  Password is not required if using API token.
     *
     * @param Guzzle $guzzle
     * @param ResponseFactory $responseFactory
     * @param string $freshdeskDomain
     * @param string $usernameOrToken
     * @param string $password
     
    public function __construct($guzzle,$freshdeskDomain, $Token, $password = "X")
    {
        $this->guzzle = $guzzle;
        $this->setFreshdeskDomain($freshdeskDomain);
        $this->setToken($Token);
        $this->setPassword($password);
    }
*/
    /**
     * Set Freshdesk username or API token.
     *
     * @param string $usernameOrToken
     */
    public function getUsers($user_email){
        $uri= urlencode($user_email);
        $url='https://tpoconcierge.freshdesk.com/api/v2/contacts?email='.$uri;
        $result= $this->sendRequest('GET',$url);
        return $result;
    }
    public function getUsersbyCompany($id_company,$page=1){        
        $url='https://tpoconcierge.freshdesk.com/api/v2/contacts?company_id='.$id_company.'&per_page=100&page='.$page;
        $result= $this->sendRequest('GET',$url);
        return $result;
    }
    public function getUser($user_id){
        $url='https://tpoconcierge.freshdesk.com/api/v2/contacts/'.$user_id;
        $result= $this->sendRequest('GET',$url);
        return $result;
    }
    
    public function getTicketList($requester){
        $uri=urlencode($requester);
        $url='https://tpoconcierge.freshdesk.com/api/v2/tickets?requester_id='.$requester.'&include=description';
        $result= $this->sendRequest('GET',$url);        
        return $result;              
    }
    
    public function getTicket($idTicket){
        $url='https://tpoconcierge.freshdesk.com/api/v2/tickets/'.$idTicket.'/conversations';        
        $result= $this->sendRequest('GET',$url);        
        return $result;              
    }
    public function getTicketDetail($idTicket){
        //$url='https://tpoconcierge.freshdesk.com/api/v2/tickets/'.$idTicket.'?include=company';
        $url='https://tpoconcierge.freshdesk.com/api/v2/tickets/'.$idTicket;
        $result= $this->sendRequest('GET',$url);        
        return $result; 
    }
    
    public function getProducts(){
        $url='https://tpoconcierge.freshdesk.com/api/v2/products';        
        $result= $this->sendRequest('GET',$url);        
        return $result;              
    }
    
    public function getCompanies(){
        $url='https://tpoconcierge.freshdesk.com/api/v2/companies';        
        $result= $this->sendRequest('GET',$url);        
        return $result;              
    }
    
    public function getCategories(){
       $url='https://tpoconcierge.freshdesk.com/api/v2/solutions/categories';  
       $result= $this->sendRequest('GET',$url);
       return $result;
   }
   public function getCategoriesEvents(){
       //fd_articles.category_id => 35000132685 : Live EVENTS
       $url='https://tpoconcierge.freshdesk.com/api/v2/solutions/categories/35000132685';  
       $result= $this->sendRequest('GET',$url);
       return $result;
   }

    public function getFolders($id_category){
        $url='https://tpoconcierge.freshdesk.com/api/v2/solutions/categories/'.$id_category.'/folders';  
        $result= $this->sendRequest('GET',$url);
        return $result;
    }
    
    public function getArticles($id_folder,$lang = 'en'){
        $id=urlencode('35000042381');
        $url='https://tpoconcierge.freshdesk.com/api/v2/solutions/folders/'.$id_folder.'/articles/'.$lang;  
        $result= $this->sendRequest('GET',$url);
        return $result;
    }
    
    public function getArticle($id_article,$lang='en'){
        $url='https://tpoconcierge.freshdesk.com/api/v2/solutions/articles/'.$id_article.'/'.$lang;  
        $result= $this->sendRequest('GET',$url);
        return $result;
    }
    public function getArticleTags($id_article,$lang='en'){
        $url='https://tpoconcierge.freshdesk.com/api/v2/solutions/articles/'.$id_article.'/'.$lang;  
        $result= $this->sendRequest('GET',$url);
        if (isset($result['tags']) && count($result['tags'])>0){
            return $result['tags'];
        }
        return FALSE;
    }
    
    public function addContact($content){    
        $url='https://tpoconcierge.freshdesk.com/api/v2/contacts';
        $client = new GuzzleHttp\Client();
        
        $api_key = $this->Token;
        $password = $this->password;
        $method="POST";
        
        //$result= $this->sendRequest('POST',$url);
        try{
            $response = $client->request($method, $url,[
                        'json' => $content,
                        'auth' => [$api_key, $password]
                    ]);
            $body = $response->getBody();
            $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
            //$tab=json_decode($body, true);
            return $tab;
        } catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    public function updateContact($user_id,$content){
    
        $url='https://tpoconcierge.freshdesk.com/api/v2/contacts/'.$user_id;
        $result= $this->sendRequest('PUT',$url,$content);
        return $result;
    }
    
    public function addTicket($content){
        $url='https://tpoconcierge.freshdesk.com/api/v2/tickets';
        $client = new GuzzleHttp\Client();
        
        $api_key = $this->Token;
        $password = $this->password;
        $method="POST";
        
        //$result= $this->sendRequest('POST',$url);
        try{
            $response = $client->request($method, $url,[
                        'json' => $content,
                        'auth' => [$api_key, $password],
                        "http_errors" => false
                    ]);
            $body = $response->getBody();
            $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
            //$tab=json_decode($body, true);
            return $tab;
        } catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function updateTicket($ticket_id,$content){
    
        $url='https://tpoconcierge.freshdesk.com/api/v2/tickets/'.$ticket_id;
        $result= $this->sendRequest('PUT',$url,$content);
        return $result;
        
    }
    
    public function closeTicket($ticket_id,$content){
        $url='https://tpoconcierge.freshdesk.com/api/v2/tickets/'.$ticket_id;
        $client = new GuzzleHttp\Client();
        
        $api_key = $this->Token;
        $password = $this->password;
        $method="PUT";
        
        //$result= $this->sendRequest('POST',$url);
        try{
            $response = $client->request($method, $url,[
                        'json' => $content,
                        'auth' => [$api_key, $password],
                        "http_errors" => false
                    ]);
            $body = $response->getBody();
            $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
            //$tab=json_decode($body, true);
            return $tab;
        } catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function addTicketReply($ticket_id,$content){
        $url='https://tpoconcierge.freshdesk.com/api/v2/tickets/'.$ticket_id.'/reply';
        $client = new GuzzleHttp\Client();
        
        $api_key = $this->Token;
        $password = $this->password;
        $method="POST";
        
        //$result= $this->sendRequest('POST',$url);
        try{
            $response = $client->request($method, $url,[
                        'json' => $content,
                        'auth' => [$api_key, $password],
                        "http_errors" => false
                    ]);
            $body = $response->getBody();
            $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
            //$tab=json_decode($body, true);
            return $tab;
        } catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function addNoteTicket($ticket_id,$content){
        $url='https://tpoconcierge.freshdesk.com/api/v2/tickets/'.$ticket_id.'/notes';
        $client = new GuzzleHttp\Client();
        
        $api_key = $this->Token;
        $password = $this->password;
        $method="POST";
        
        //$result= $this->sendRequest('POST',$url);
        try{
            $response = $client->request($method, $url,[
                        'json' => $content,
                        'auth' => [$api_key, $password],
                        "http_errors" => false
                    ]);
            $body = $response->getBody();
            $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
            //$tab=json_decode($body, true);
            return $tab;
        } catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function postStatisfactionRating($ticket_id,$content) {
  
        
        $url='https://tpoconcierge.freshdesk.com/api/v2/tickets/'.$ticket_id.'/satisfaction_ratings';
        $client = new GuzzleHttp\Client();
        
        $api_key = $this->Token;
        $password = $this->password;
        $method="POST";
        
        //$result= $this->sendRequest('POST',$url);
        try{
            $response = $client->request($method, $url,[
                        'json' => $content,
                        'auth' => [$api_key, $password],
                        "http_errors" => false
                    ]);
            $body = $response->getBody();
            $tab=json_decode($body, true, 512, JSON_BIGINT_AS_STRING);
            //$tab=json_decode($body, true);
            return $tab;
        } catch(Exception $e){
            return $e->getMessage();
        }
      
    }
    public function getAgents(){
        $url='https://tpoconcierge.freshdesk.com/api/v2/agents?per_page=100'; 
        $result= $this->sendRequest('GET',$url);
        return $result;
    }
    
    protected function sendRequest($method,$uri,$content=null) {
        $client = new GuzzleHttp\Client();
        $api_key = $this->Token;
        $password = $this->password;
        try{
            $response = $client->request($method, $uri,[
                        'json' => $content,
                        'auth' => [$api_key, $password]
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

