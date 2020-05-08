 <?php

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



$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
$codeLang = $_SESSION['lang']['code'];

require_once("../includes/fct_security.php");
$permissions = 0;

if(isset($user) && $user->isLoggedIn()){
     if (ip_blocked()){
        $user->logout();
        Redirect::to('blocked.php');        
    }
    else {
        $account = $user->data();
        $fname=$account->fname;$lname=$account->lname;
        $email=$account->email;
        $uid=$account->fd_id;
        $permissions = $account->permissions;
        $Logged = createName($fname, $lname, $codeLang);
    }
}
else {
    $Logged=false;
    if ($GLOBALS['company']['redirect']){
        Redirect::to('soon.php');
    }
    If ($GLOBALS['company']['unlogged_access'] == false){
        Redirect::to('login.php');
    }
}
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<!-- wrapper -->
<div id="wrapper">    			
   <?php echo display_navbar($Logged,$permissions); ?>
    
    <!-- HOME -->

    <?php  if($Logged){
               $fullname= createName($fname, $lname, $codeLang);
               if(isBrowserIE() && $GLOBALS['company']['request_form']){
                   echo displaySectionHomeRequestForm($uid);
               }
               elseif($GLOBALS['company']['id'] == '35000564654'){
                   unset($_SESSION['Guests']);
                   echo displaySectionHomeRequestFormEnhanced($uid);
               }else{
                   echo displaySectionHomeLogged($idLang,$fullname,$email);
               }
           } else{
               echo displaySectionHomeUnLogged($idLang);
           }?>


     <!-- /HOME -->

<!--section Requests start-->
        <?php 
        if ($Logged) {
            echo displaySectionRequests();
            if ($GLOBALS['company']['sections']['blog']){echo displaySectionBlog();}
            if ($GLOBALS['company']['sections']['event']) {echo displaySectionEvents();}
            if ($GLOBALS['company']['sections']['how_to']){echo displaySectionHowto(); }
            if ($GLOBALS['company']['sections']['example']){echo displaySectionExample();}
            if ($GLOBALS['company']['sections']['about']){echo displaySectionAbout(); } 
            if ($GLOBALS['company']['sections']['team']){echo displaySectionTeam(); }
            if ($GLOBALS['company']['sections']['faq']){echo displaySectionFAQ(); }
            if ($GLOBALS['company']['sections']['contact']) {echo displayContact();}
            
        } else {
            if ($GLOBALS['company']['sections']['blog']){echo displaySectionBlog();}
            if ($GLOBALS['company']['sections']['event']) {echo displaySectionEvents();}
            if ($GLOBALS['company']['sections']['contact']) {echo displayContact();}            
            if ($GLOBALS['company']['sections']['how_to']){echo displaySectionHowto(); }
            if ($GLOBALS['company']['sections']['example']){echo displaySectionExample();}
            if ($GLOBALS['company']['sections']['about']){echo displaySectionAbout(); }
            if ($GLOBALS['company']['sections']['team']){echo displaySectionTeam(); }
            if ($GLOBALS['company']['sections']['card']){echo displaySectionCards(); }
        } ?>


<!-- Modal start -->
        <?php echo displayModal(); ?>
<!-- Modal end -->

<?php require_once '../includes/footer.php';  ?>

</div>
<script>
        function setStarsLoop(ticket,value,max) {
            resetStarColors(ticket);
            for (var i=0; i <= max; i++){
                if (i <= value){
                    $(ticket+':eq('+i+')').css('color', '#fcc203');
                }
                else {
                    $(ticket+':eq('+i+')').css('color', 'grey');
                }
            }
        }
        function setStars(ticket,value,max) {
            resetStarColors(ticket);
            for (var i=0; i <= max; i++){
                if (i == value){
                    $(ticket+':eq('+i+')').css('color', '#fcc203');
                }
                else {
                    $(ticket+':eq('+i+')').css('color', 'grey');
                }
            }
        }

        function resetStarColors(ticket='.fa') {
            $(ticket).css('color', 'grey');
        }
    </script>

    <?php
        $jaxon = jaxon();
        echo $jaxon->getJs();
        echo $jaxon->getScript();
    ?>
    <?php if ($Logged) {
                if (isset($_GET['rqt']) && !empty($_GET['rqt'])) {
                    $param = $uid.",".$_GET['rqt'];
                }else{
                    $param = $uid;
                }
    ?>
                <script type='text/javascript'>
                    document.addEventListener('DOMContentLoaded', function() {
                         // your code here
                         window.jaxon_initPage(<?php echo $param ; ?>);
                    }, false);
                    
                </script>
    <?php } ?>

<!-- SCROLL TO TOP -->
    <a href="#" id="toTop"></a>
<script>
        $( function() {

            // Single Select
            $( "#input_guest" ).autocomplete({
                source: function( request, response ) {
                    // Fetch data
                    $.ajax({
                     url: 'ajax.php',
                     type: 'post',
                     dataType: "json",
                     data: {
                      searchguest: request.term
                     },
                     success: function( data ) {
                      response( data );
                     }
                    });
                },
                select: function (event, ui) {
                 // Set selection
                 var user_data = ui.item.label.split(" - ");
                 $('#input_guest').val(ui.item.label); // display the selected text                 
                 $('#customer_id').val(ui.item.value); // save selected id to input
                 //$('#input_guest').val(user_data[1]);
                 
                 console.log(ui.item.label, ui.item.value);
                 return false;
                }
            });
            
           });
           
</script>
    <!-- PRELOADER -->
    <div id="preloader">
        <div class="inner">
            <span class="loader"></span>
        </div>
    </div>
    <!-- /PRELOADER -->

    <!-- JAVASCRIPT FILES -->
    <script>var plugin_path = 'assets/plugins/';</script>
    <script type="text/javascript" src="assets/plugins/form.validate/jquery.validation.min.js"></script>

    <script src="assets/js/scripts.js"></script> 

  </body>
</html>


