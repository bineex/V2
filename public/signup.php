<?php
date_default_timezone_set('UTC');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../../../vendor/autoload.php';

//Ajax Librairy ----
// Get the core singleton object
// and the Response class
 
ini_set("allow_url_fopen", 1);
//if(isset($_SESSION)){session_destroy();}

use Jaxon\Jaxon;
use Jaxon\Response\Response;

require_once '../init.php';


require_once("../includes/fct_account.php");
require_once("../includes/fct_display.php");
require_once("../includes/class_freshdesk.php");

require_once("../includes/fct_security.php");
if (ip_blocked()){
    $user->logout();
    Redirect::to('blocked.php');
}

$jaxon = jaxon();
$jaxon->processRequest();// Call the Jaxon processing engine

require_once '../includes/header.php';
//require_once '../includes/navigation.php';

$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
$codeLang = $_SESSION['lang']['code'];

if($user->isLoggedIn()) Redirect::to('index.php');

$error_message="";
$g="";
if(isset($_GET['session_id']) && !empty($_GET['session_id'])){
    require 'stripe/config.php';
    \Stripe\Stripe::setApiKey($config['stripe_secret_key']);
    
    $id = $_GET['session_id'];
    $checkout_session = \Stripe\Checkout\Session::retrieve($id);
    
    $subscription = \Stripe\Subscription::retrieve($checkout_session->subscription);
    $query = $db->insert("subscriptions",
                ['id' => $checkout_session->subscription,
                 'fd_id'=>$checkout_session->client_reference_id, 
                 'customer'=>$checkout_session->customer,
                 'email' => $checkout_session->customer_email,
                 'plan' => $subscription->items->data[0]->plan->id,
                 'status'=>$subscription->status,
                 'date_start' => date('Y-m-d H:i:s ',$subscription->start_date),
                 'date_created'=>date('Y-m-d H:i:s ',$subscription->created), 
                 'date_canceled'=>date('Y-m-d H:i:s ',$subscription->cancel_at)
                ],TRUE);
    
    $_SESSION['JUSTSIGNED']=1;
    Redirect::to('login.php?s=1&u='.$checkout_session->client_reference_id);
    
}
if(isset($_GET['cancel_id']) && !empty($_GET['cancel_id'])){
    $cancel_id = $_GET['cancel_id'];
    $id = substr($cancel_id, strpos($cancel_id, '-') + 1);
    $fd_id = substr($cancel_id,0,strpos($cancel_id, '-'));
    $re = $db->delete("users",["and", ["id","=",$id], ["fd_id","=",$fd_id]]);
    Redirect::to('index.php');

}
if(isset($_POST['-fd_id']) && !empty($_POST['-fd_id'])){
    $nextStep=3;
    
    //Log user in          
    $tab['active']=true;
    foreach ($_POST as $key => $value) {
        if(isset($value) && !empty($value)){

            if (substr($key,0,1) == '_'){
                $k=substr($key, 1);
                $customField[$k]=$value;

            } elseif (substr($key,0,1)=='-'){
                //nada
            } elseif (substr($key,0,3)=='tag'){
                $tags[] = $value;
            } else {
                $tab[$key]=$value;
            }
        }
    }                        

    //$tab['view_all_tickets']=false;
    $tab['language'] = $codeLang;
    if (isset($tags) && !empty($tags)){
        $tab['tags'] = $tags;
    }
    $tab['name'] = createName($_POST['_first_name_kanjiromaji'], $_POST['_family_name_kanjiromaji'], $codeLang);

    if (isset($_POST['-rand981955'])){
        $birthday = isDate($_POST['-rand981955']);
        if ($birthday!=false){
            $customField['rand981955']=$birthday;
        } else {
            $customField['birthdaytxt']=$_POST['-rand981955'];
        }
    }
                        
    $user_id=$_POST['-fd_id'];
    $customField['signedup'] = true;
    $customField['registration_date'] = date("Y-m-d");

    if(isset($_POST['-company_additional']) && intval($_POST['-company_additional']) > 1){
        $company_add[] = array ("company_id" => intval($_POST['-company_additional']));
        $tab['other_companies'] = $company_add;
    }

    if($user_id == 'new'){
        
        if (isset($_POST['-emailcustom']) && !empty($_POST['-emailcustom'])){
            $email = $_POST['-emailcustom'];
        }
        else {
            $email = $_POST['-email'];
        }
        
        $tab['email'] = $email;
        $tab['company_id']=intval($company['id']);

        $customField['walkin']=true;
        $tab['custom_fields']=$customField; 
        //$content= json_encode($tab);

        $fdUsers= new freshdesk();    
        $fd_values = $fdUsers->addContact($tab);

        if (isset($fd_values['id']) && !empty($fd_values['id'])){
            $fd_id = $fd_values['id'];
        }

    } else {
        
        $tab['custom_fields']=$customField;

        $fd_id = $_POST['-fd_id'];
        //$content= json_encode($tab);                        
        $fdUsers= new freshdesk();    
        $fd_values = $fdUsers->updateContact($fd_id,$tab);
        $email = $_POST['-email'];
 
    }
    //debug_to_console( json_encode($fd_values) );

    file_put_contents('log/log_signup_'.date("y-n-j").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [REQT] '. json_encode($tab,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
    file_put_contents('log/log_signup_'.date("y-n-j").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [RESP] '. json_encode($fd_values,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);               

    if (isset($fd_id) && !empty($fd_id)){
        $user = new User();

        $vericode = randomstring(15);
        $vericode_expiry=date("Y-m-d H:i:s");

        $user_id = $user->create(array(
                'username' => $_POST['-email'],
                'fd_id' => $fd_id,
                'fname' => $_POST['_first_name_kanjiromaji'],
                'lname' => $_POST['_family_name_kanjiromaji'],
                'email' => $email,
                'email_work' => $_POST['-email'],
                'password' => password_hash($_POST['-password'], PASSWORD_BCRYPT, array('cost' => 12)),
                'permissions' => 1,
                'account_owner' => 1,
                'join_date' => $vericode_expiry,
                'email_verified' => 1,
                'active' => 1,
                'vericode' => $vericode,
                'vericode_expiry' => $vericode_expiry
        ));
        
        $user_id = $fd_id;
        
        if(isset($company['subscription_id']) && $company['subscription_id']){
            
               $_SESSION['subscription']['email'] = $_POST['-email'];
               $_SESSION['subscription']['fd_id'] = $fd_id;
               $_SESSION['subscription']['user_id'] = $user_id;
               $_SESSION['subscription']['plan'] = $company['subscription_id']; 
?>
               <script src="https://js.stripe.com/v3/"></script>
               <script src="stripe/stripe_script.js" defer></script>
<?php

        } else {
            $_SESSION['JUSTSIGNED']=1;
            Redirect::to('login.php?s=1&u='.$user_id);
        }

    }
                        

      


}

//print_r($lang);
$Logged=false;
if(isset($user) && $user->isLoggedIn()){
    $Logged=true;
    Redirect::to('index.php');
}

?>
<!-- wrapper -->
<div id="wrapper">
    <?php echo display_navbar_short(); ?>
    
    <section id="signup">
            <div class="container">
                <div id='contentForm'>
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <!-- ALERT -->
                                <?php if(!$error_message=='') {?>
                                <div class="alert alert-mini alert-danger mb-30">
                                        <?=$error_message;?>
                                </div>
                                <?php } ?>
                                <!-- /ALERT -->
                            <div class="box-static box-border-top p-30">
                                <div class="box-title mb-30">
                                        <h2 class="fs-20"><?php echo $lang['SIGNUP_TEXT']; ?></h2>
                                </div>
                                <form id="form_account" class="m-0" onsubmit="return jaxon_SignUp(jaxon.getFormValues('form_account'))">
 
                                    <div class="clearfix">
                                            <div class="form-group">
                                                    <label for="email"><?php echo $lang['FRM_EMAIL_CUSTOM'] ; ?> </label>
                                                    <input id="email" name="email" type="text" class="form-control" required />
                                            </div>
                             
                                        <?php if ($lang['FRM_EMPLOYEE_ID']) {?>                                            
                                            <div class="form-group">
                                                            <label for="employee_id"><?php echo $lang['FRM_EMPLOYEE_ID'].'<br>'.$lang['FRM_EMPLOYEE_ID_DESC'] ; ?></label>
                                                            <input id="employee_id" name="employee_id" type="text" class="form-control" required />
                                            </div>
                                            
                                        <?php }?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-6 text-right">
                                            <button type="submit" class="btn btn-primary btn-lg-xs"><?php echo $lang['SIGNUP_BUTTON_STP1'] ; ?></button>
                                            
                                        </div>
                                    </div>
                            </form>
                            </div>
                        </div>
                   </div>
                </div>
            </div>
            <!--container-->
        </section>
   
<?php require_once '../includes/footer.php';  ?>
    
</div>
<?php
    $jaxon = jaxon();
    echo $jaxon->getJs();
    echo $jaxon->getScript();
?>

    
<?php require_once '../includes/html_footer.php'; ?>
