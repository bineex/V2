<?php
date_default_timezone_set('UTC');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../../../vendor/autoload.php';
require_once '../init.php';
require_once '../includes/header.php';
//<script src="stripe/script.js" defer></script>
?>
<?php

$_SESSION['subscription']['email'] = "mario@testing.com";
$_SESSION['subscription']['fd_id'] = "35030075657";
$_SESSION['subscription']['user_id'] = "3481";
//PROD
$_SESSION['subscription']['plan'] = 'family1';
$_SESSION['subscription']['tax'] = 'txr_1GSCfYF8FRDOm1FXJ7TSf3k7';

//PPE
$_SESSION['subscription']['plan'] = 'plan_H13OERHhFnQddF';
$_SESSION['subscription']['tax'] = 'txr_1GSVUeF8FRDOm1FXNHo5BYOa';


?>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="stripe/stripe_script.js" defer></script>
              
<?php
    /*
    echo 'session:<br />';
    $checkout_session = \Stripe\Checkout\Session::retrieve('cs_test_mVjXXYM1StvWykTDU8Wy0B4DPo8ldmT5Tgc6JaEbWN7jddnwSrBfutmd');
    echo json_encode($checkout_session);
    echo '<br /><br />';
    echo 'Subscription:'.$checkout_session->subscription.'<br />';
    echo '<br />';
    echo 'Subscription:<br />';
    $sub = \Stripe\Subscription::retrieve('sub_GzBC2luf087Vc8');
    //print_r($sub);
    $j_sub = json_encode($sub);
    print_r($sub);
    echo '<br /><br />';
    echo 'Subscription status:'.$sub->status.'<br />';
    echo 'Subscription date:'.date('Y-m-d H:i:s ',$sub->start_date).'<br />';
    echo 'Plan:'.$sub->items->data[0]->plan->id.'<br />';
     * 
     */
?>
  </body>
</html>