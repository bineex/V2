<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../init.php';
require_once '../includes/fct_ajax.php';

// Get search term
//$searchTerm = $_GET['term'];
if(isset($_POST['search']) && !empty($_POST['search'])){
    $searchTerm = $_POST['search'];
    echo searchUsers($searchTerm);
}

if(isset($_POST['searchguest']) && !empty($_POST['searchguest'])){
    $searchTerm = $_POST['searchguest'];
    echo searchGuest($searchTerm);
}
exit;