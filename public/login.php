<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';

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

require_once("../includes/fct_security.php");
if (ip_blocked()){
    $user->logout();
    Redirect::to('blocked.php');        
}

$jaxon = jaxon();
$jaxon->processRequest();// Call the Jaxon processing engine

require_once '../includes/header.php';

$query_string = $_SERVER['QUERY_STRING'];

if($user->isLoggedIn()) {
    If (isset($_SESSION['goto']) && !empty($_SESSION['goto'])){
        $goto = $_SESSION['goto'];
        unset($_SESSION['goto']);
        Redirect::to($goto);
    }
    else{
        $_SESSION['last_confirm']=date("Y-m-d H:i:s");
        Redirect::to('index.php?'.$query_string);
    }
}

$error_message="";
if(Input::exists('post') && isset($_POST['username'])){
     
    $user = new User();
    $loginbymail = $user->loginEmail(Input::get('username'), trim(Input::get('password')));
    //$loginbyuser = $user->login(Input::get('username'), trim(Input::get('password')));
    if ($loginbymail) {
        $account = $user->data();
        $uid=$account->fd_id;
        $login_success = TRUE;
        
        if(!empty($company['signup_subscription'])){
            
                $plan = $company['subscription_id'];
                $subscription_status = checkSubscriptionStatus($uid,$plan);
                
                if(empty($subscription_status)){
                        $error_message .= '<strong>'.$lang['SIGNIN_FAIL'].'</strong><br>'.$lang['SUBSCRIPTION_STATUS_INVALID'];
                        $login_success = FALSE;
                        $user->logout();

                }            
        }

        if(isset($company['membership_check']) && $company['membership_check']){
            $membership_status = getMembershipStatus($uid);
                if ($membership_status == 'Inactive'){
                $error_message .= '<strong>'.$lang['SIGNIN_FAIL'].'</strong><br>'.$lang['SIGNIN_FAIL_MEMBERSHIP'];
                $user->logout();
                $login_success = FALSE;
            }
        }

    } else {
      $login_success = FALSE;
      $error_message .= '<strong>'.$lang['SIGNIN_FAIL'].'</strong><br>'.$lang['SIGNIN_FAIL_MSG'];
    }
    if ($login_success){
        $_SESSION['last_confirm']=date("Y-m-d H:i:s");
        if (isset($_SESSION['goto']) && !empty($_SESSION['goto'])){
            $goto = $_SESSION['goto'];
            unset($_SESSION['goto']);
            Redirect::to($goto);
        }
        else{            
            Redirect::to('index.php?'.$query_string);
        }
    }
}


?>

<!-- wrapper -->
<div id="wrapper">
  <?php echo display_navbar_short(); ?>

<!--NAVBAR-->

    <section>
        <div class="container">
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
                                                <h2 class="fs-20"><?php echo $lang['SIGNIN_TEXT']; ?></h2>
                                        </div>

                                        <form id="form_account" name="form_account" class="m-0" method="post" action="login.php?<?php echo $query_string; ?>" autocomplete="off">
                                                <div class="clearfix">

                                                        <!-- Email -->
                                                        <div class="form-group">
                                                                <input type="text" name="username" id="username" class="form-control" placeholder="<?php echo $lang['FRM_EMAIL']; ?>" required="">
                                                        </div>

                                                        <!-- Password -->
                                                        <div class="form-group">
                                                                <input type="password" name="password" id="password" class="form-control" placeholder="<?php echo $lang['FRM_PASSWORD']; ?>" required="">
                                                        </div>

                                                </div>

                                                <div class="row">

                                                        <div class="col-md-6 col-sm-6 col-6">

                                                                <!-- Inform Tip -->                                        
                                                                <div class="form-tip pt-20">
                                                                        <a class="no-text-decoration txt-info fs-15 mt-10 block" href="forgotpass.php"><?php echo $lang['PASSWORD_FORGOT']; ?></a>
                                                                        <a class="no-text-decoration txt-info fs-15 mt-10 block" href="signup.php"><?php echo $lang['SIGNUP_TEXT']; ?></a>
                                                                </div>
                                                        </div>

                                                        <div class="col-md-6 col-sm-6 col-6 text-right">

                                                                <button class="btn btn-primary"><?php echo $lang['SIGNIN_TEXT']; ?></button>

                                                        </div>

                                                </div>

                                        </form>

                                </div>

                        </div>
                </div>

        </div>
    </section>
<!-- Modal start -->
        <?php echo displayModalSignup(); ?>
<!-- Modal end -->

<?php require_once '../includes/footer.php';  ?>

</div>



<!-- footers -->
<?php if (isset ($_SESSION['JUSTSIGNED']) && $_SESSION['JUSTSIGNED']==1 || isset($_GET['s'])){
        echo $jaxon->getJs();
        echo $jaxon->getScript();
     
        $_SESSION['JUSTSIGNED']=0;
        if(isset($_GET['u']) && !empty($_GET['u']) && $GLOBALS['company']['event']){
            $user_id = $_GET['u'];
            $event = $GLOBALS['company']['event'];?>
            <script type="text/javascript">window.jaxon_displayWelcomeEvent("<?php echo $user_id.'","'.$event; ?>");</script>
<?php } else {?>
            <script type="text/javascript">window.jaxon_displayWelcome();</script>
<?php }}?>

<!-- Place any per-page javascript here -->


<?php require_once '../includes/html_footer.php'; // currently just the closing /body and /html ?>
