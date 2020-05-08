<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../../vendor/autoload.php';

//Ajax Librairy ----
// Get the core singleton object
// and the Response class

use Jaxon\Jaxon;
use Jaxon\Response\Response;

require_once '../init.php';
require_once("../includes/fct_freshdesk.php");
require_once("../includes/fct_account.php");
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
}

?>

<!-- wrapper -->
<div id="wrapper">
  <?php echo display_navbar(); ?>

<!--NAVBAR-->

    <section>
        <div class="container">
                <div class="row">
                        <div class="col-md-6 offset-md-3">
                                <div class="box-static box-border-top p-30">
                                        <div class="box-title mb-30">
                                                <h2 class="fs-20"><?php echo $lang['FORM_CONTACT_TITLE']; ?></h2>
                                                
                                        </div>
                                        <h5><?php echo $lang['FORM_CONTACT_SUBTITLE']; ?></h5>
                                        <form id="form_contact" name="form_contact" class="m-0" onsubmit="return jaxon_sendContactRequest(jaxon.getFormValues('form_contact'))">
                                                
                                            <div class="clearfix">
                                                       <div class='form-row'>
                                                            <div class='col'>
                                                              <label for='family_name'><?php echo $lang['FRM_FAMILY_NAME']; ?></label>
                                                              <input type='text' class='form-control' id='family_name' name='family_name' required >
                                                            </div>
                                                            <div class='col'>
                                                              <label for='first_name'><?php echo $lang['FRM_FIRST_NAME']; ?></label>
                                                              <input type='text' class='form-control' id='first_name' name='first_name' required >
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div class='form-row'>
                                                            <div class='col'>
                                                              <label for='email'><?php echo $lang['FORM_CONTACT_EMAIL']; ?></label>
                                                              <input type='text' class='form-control' id='email' name='email' required>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div class='form-row'>
                                                            <div class='col'>
                                                              <label for='sendersubject'><?php echo $lang['FORM_CONTACT_SUBJECT']; ?></label>
                                                              <input type='text' class="form-control" id="sendersubject" name="sendersubject" required >                                                           </div>
                                                        </div>
                                                        <br>
                                                        <div class='form-row'>
                                                            <div class='col'>
                                                              <label for='sendermessage'><?php echo $lang['FORM_CONTACT_DETAIL']; ?></label>
                                                              <textarea rows="5" class="form-control" id="sendermessage" name="sendermessage" required></textarea>
                                                             </div>
                                                        </div>
                                                        <br>
                                                        <div class='form-footer text-center'>
                                                            <button type='submit' class='btn btn-primary' ><?php echo $lang["REQUEST_SEND_BUTTON"]; ?></button>
                                                        </div>
                                                </div> 
                                        </form>
                                </div>
                        </div>
                </div>
        </div>
    </section>
<!-- FOOTER -->
    <!--footer start-->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-5 margin-b-30 order-xs-3">
                    <span>&copy; Copyright 2018 - 2019. <?php echo $lang['COPYRIGHT']; ?></span>
                </div>
                <!--copyright col-->
                <div class="col-sm-7 text-right">
                    <ul class="float-right m-0 list-inline mobile-block">
                            <li><a href='<?php echo "documents/".$GLOBALS['company']['id']."/".$GLOBALS['company']['t&c_doc'] ?>' target='_blank' rel='nofollow'><?php echo $lang['TERMS_CONDITIONS']; ?></a></li>
                            <?php if ($GLOBALS['company']['privacy_doc']) {?>
                            <li>&bull;</li>
                            <li><a href='<?php echo "documents/【SYSTEM SUPPORT】Privacy Policy_2019.05.pdf" ?>' target='_blank' rel='nofollow'><?php echo $lang['PRIVACY']; ?></a></li>
                            <?php } ?>
                    </ul>
                </div>
                <!--footer nav col-->
            </div>
            <!--row-->
        </div>
        <!--container-->
    </footer>
<!--footer end-->

</div>
 <?php
        $jaxon = jaxon();
        echo $jaxon->getJs();
        echo $jaxon->getScript();
 ?>
<?php require_once '../includes/html_footer.php'; // currently just the closing /body and /html ?>
