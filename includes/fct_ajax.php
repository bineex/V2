<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function searchUsers($searchTerm){


    $db = DB::getInstance();
     $sql="SELECT * from users
          where (email like '%".$searchTerm."%')
             OR (fname like '%".$searchTerm."%')
             OR (lname like '%".$searchTerm."%') 
             OR (email_work like '%".$searchTerm."%')
             order by email_work";

    //$sql="SELECT * from users";

     $db->query($sql);
     $row = $db->results();

    $usersData = array();
    $response = array();

    foreach ($row as $value) {
        $label = $value->lname ." ".$value->fname." - ".$value->email;
        $response[] = array("value"=>$value->fd_id,"label"=>$label);

        $data['id'] = $value->fd_id;
        $data['value'] = $value->lname ." ".$value->fname." - ".$value->email;
        array_push($usersData, $data);
    }

    //$response[] = array("value"=>"1235677","label"=>"Testing");
    return json_encode($response);
}

function searchManagers($searchTerm){


    $db = DB::getInstance();
    $company = $GLOBALS['company']['id'];
    
    $sql="SELECT * from company_managers
          where company_id = ".$company." AND ((first_name like '%".$searchTerm."%')OR (last_name like '%".$searchTerm."%'))
          order by fname,";

    //$sql="SELECT * from users";

     $db->query($sql);
     $row = $db->results();

    $usersData = array();
    $response = array();

    foreach ($row as $value) {
        $label = $value->lname ." ".$value->fname." - ".$value->email;
        $response[] = array("value"=>$value->fd_id,"label"=>$label);

        $data['id'] = $value->fd_id;
        $data['value'] = $value->lname ." ".$value->fname." - ".$value->email;
        array_push($usersData, $data);
    }

    //$response[] = array("value"=>"1235677","label"=>"Testing");
    return json_encode($response);
}

function searchGuest($searchTerm){

    $db = DB::getInstance();
    $company = $GLOBALS['company']['id'];
    
    $sql="SELECT * from company_customers
          where company_id = ".$company."
            AND ((customer_company_name like '%".$searchTerm."%') OR (customer_name like '%".$searchTerm."%') OR (customer_department like '%".$searchTerm."%'))
          order by customer_company_name,customer_department,customer_name";

    //$sql="SELECT * from users";

     $db->query($sql);
     $row = $db->results();

    $usersData = array();
    $response = array();

    foreach ($row as $value) {
        $label = $value->customer_company_name ." ".$value->customer_department." - ".$value->customer_name;
        $response[] = array("value"=>$value->customer_id,"label"=>$label);

        $data['id'] = $value->customer_id;
        $data['value'] = $value->customer_company_name ." ".$value->customer_department." - ".$value->customer_name;
        array_push($usersData, $data);
    }

    //$response[] = array("value"=>"1235677","label"=>"Testing");
    return json_encode($response);
}