 <?php

require '../vendor/autoload.php';

//Ajax Librairy ----
// Get the core singleton object
// and the Response class

use Jaxon\Jaxon;
use Jaxon\Response\Response;

require_once("../init.php");
require_once("../includes/fct_freshdesk.php");
require_once("../includes/fct_account.php");
require_once("../includes/fct_display.php");

$jaxon->processRequest();// Call the Jaxon processing engine

require_once '../includes/header.php';

//print_r($GLOBALS['company']);


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


     <!-- /HOME 
     <div class="sticky-side sticky-side-left visible-md visible-lg">
				<a href="https://www.facebook.com/TPOInc/" class="social-icon social-icon-light  social-facebook">
					<i class="icon-facebook"></i>
					<i class="icon-facebook"></i>
				</a>
				<a href="https://twitter.com/TPO_Concierge" class="social-icon social-icon-light  social-twitter">
					<i class="icon-twitter"></i>
					<i class="icon-twitter"></i>
				</a>
			</div>
    -->
<!--section Requests start-->
        <?php
        switch ($company['id']) {
            case '35001064691':
                if ($Logged) {
                    echo displaySectionRequests($uid);
                    echo displaySectionBlog();
                    echo displaySectionEvents();
                    echo displaySectionEventsPayment();
                    echo displaySectionHowtoSignup();
                    echo displaySectionFAQ();
                    echo displayContact();                    
                } else {
                    echo displaySectionService();
                    echo displaySectionBlog();
                    echo displaySectionEvents();
                    //echo displaySectionPrograms(); 
                    echo displaySectionPrivates();
                    echo displaySectionHowtoSignup();
                    echo displaySectionPrice();
                    echo displaySectionCallout();
                    echo displaySectionFAQ();
                    echo displayContact();                   
                }
                break;
            case '35001026668':
                if ($Logged) {
                    echo displaySectionRequests($uid);
                    echo displaySectionBlog();
                    echo displaySectionEvents();
                    echo displaySectionHowtoSignup();
                    echo displaySectionPrice();
                    echo displaySectionTeam();
                    echo displaySectionFAQ();
                    echo displayContact();                    
                } else {
                    echo displaySectionBlog();
                    echo displaySectionEvents();
                    echo displaySectionHowtoSignup();
                    echo displaySectionPrice();
                    echo displaySectionTeam();
                    echo displaySectionFAQ();
                    echo displayContact();                   
                }
                break;
            case '35001261629':
                if ($Logged) {
                    echo displaySectionWeekEvents();
                    echo displaySectionRequests($uid);
                    if ($GLOBALS['company']['sections']['contact']) {echo displayContact();}
                } else {
                    if ($GLOBALS['company']['sections']['contact']) {echo displayContact();}
                }
                break;
                    
            default:
                if ($Logged) {
                    echo displaySectionRequests($uid);
                    if ($GLOBALS['company']['sections']['blog']){echo displaySectionBlog();}
                    if ($GLOBALS['company']['sections']['event']) {echo displaySectionEvents();echo displaySectionEventsPayment();}
                    if ($GLOBALS['company']['sections']['eventdirect']) {echo displaySectionWeekEvents();}
                    if ($GLOBALS['company']['sections']['how_to']){echo displaySectionHowto(); }
                    if ($GLOBALS['company']['sections']['example']){echo displaySectionExample();}
                    if ($GLOBALS['company']['sections']['about']){echo displaySectionAbout(); } 
                    if ($GLOBALS['company']['sections']['team']){echo displaySectionTeam(); }
                    if ($GLOBALS['company']['sections']['faq']){echo displaySectionFAQ(); }
                    if ($GLOBALS['company']['sections']['contact']) {echo displayContact();}                    
                } else {
                    if ($GLOBALS['company']['sections']['blog']){echo displaySectionBlog();}
                    if ($GLOBALS['company']['sections']['event']) {echo displaySectionEvents();}
                    if ($GLOBALS['company']['sections']['howto_signup']) {echo displaySectionHowtoSignup();}
                    if ($GLOBALS['company']['sections']['contact']) {echo displayContact();}
                    if ($GLOBALS['company']['sections']['how_to']){echo displaySectionHowto(); }
                    if ($GLOBALS['company']['sections']['example']){echo displaySectionExample();}
                    if ($GLOBALS['company']['sections']['about']){echo displaySectionAbout(); }
                    if ($GLOBALS['company']['sections']['team']){echo displaySectionTeam(); }
                    if ($GLOBALS['company']['sections']['card']){echo displaySectionCards(); }
                }
                break;
        }
        ?>

<!-- Modal start -->
        <?php echo displayModal(); ?>
        <?php if ($GLOBALS['company']['sections']['eventdirect']) {echo displayModalJoinDirect();}  ?>
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
        <script type="text/javascript">window.jaxon_displayTicketList(<?php echo $param ; ?>);</script>
        
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
    <!-- SWIPER SLIDER -->
    <script src="assets/plugins/slider.swiper/dist/js/swiper.min.js"></script>
    <script src="assets/js/view/demo.swiper_slider.js"></script>

    

    <script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/js/dataTables.tableTools.min.js"></script>
    <script src="assets/plugins/datatables/js/dataTables.colReorder.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript">$(document).ready( function () {
                                        $('#datatable_requests').DataTable({
                                            "order": [[ 0, 'desc' ], [ 3, 'desc' ]],
                                            "info":false,
                                            "pagingType": "numbers",
                                            "renderer": "bootstrap",
                                            "drawCallback": function() {$(this.api().table().header()).hide();}
                                        });
                                    } );
    </script>
    <?php if ($Logged && !empty($company['init_bot'])) { ?>
    <script>
        (function(){
            var w=window,d=document;
            var s="https://app.chatplus.jp/cp.js";
            d["__cp_d"]="https://app.chatplus.jp"; 
            d["__cp_c"]="fd0e4423_1";
            d["__cp_p"]={
                "chatName":"<?php echo $Logged ?>"
                , "chatEmail":"<?php echo $email ?>"
        };
        d["__cp_f"]={
            "tpoemail":"<?php echo $email ?>"
        , "language":"<?php echo $codeLang ?>"
        };
        var a=d.createElement("script"), m=d.getElementsByTagName("script")[0];
        a.async=true,a.src=s,m.parentNode.insertBefore(a,m);})();
    </script>

    <?php } ?>
  </body>
</html>