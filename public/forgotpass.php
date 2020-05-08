<?php

require '../vendor/autoload.php';
//require_once '../includes/lib_php_db.inc.php';

ini_set("allow_url_fopen", 1);

use Jaxon\Jaxon;
use Jaxon\Response\Response;

require_once '../init.php';

require_once("../includes/fct_pass.php");
require_once("../includes/fct_display.php");


$jaxon->processRequest();// Call the Jaxon processing engine

require_once '../includes/header.php';

require_once("../includes/fct_security.php");
if (ip_blocked()){
    $user->logout();
    Redirect::to('blocked.php');        
}
$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
$codeLang = $_SESSION['lang']['code'];

$Logged=false;
if(isset($user) && $user->isLoggedIn()){
    $Logged=true;
    Redirect::to('index.php');
}

?>


<div id="wrapper">
   <?php echo display_navbar_short($Logged); ?>
<!--section Requests start-->
    <section id='pricing' class='pricing-wrapper pt-100 pb-70'>
            <div class='container'> 
                <form id='form_account' name='form_account' onsubmit='return jaxon_resetPassword(jaxon.getFormValues("form_account"));'>
                    <div class='row'>
                        <div class='col-md-4 ml-auto mr-auto'>
                            <div class='price-box scrollReveal sr-scaleDown sr-ease-in-out-back'>
                                <p id='response_msg'></p>
                                <h3 id='titleForm'><?php echo $lang['PASSWORD_RESET'] ; ?></h3>                            

                                <div class='form-group'>
                                    <input type='email' id='email' name='email' class='form-control' placeholder='<?php echo $lang['FRM_EMAIL'] ; ?>' required >
                                </div>                               
                                <div class='price-footer'>                                    
                                    <button type='submit' class='btn btn-primary btn-lg-xs'><?php echo $lang['PASSWORD_RESET'] ; ?></button>
                                </div>                                                      
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>

<!-- footers -->
<?php require_once '../includes/footer.php';  ?>
</div>
<!-- Place any per-page javascript here -->
<?php
    $jaxon = jaxon();
    echo $jaxon->getJs();
    echo $jaxon->getScript();
?>

<?php require_once '../includes/html_footer.php'; // currently just the closing /body and /html ?>
