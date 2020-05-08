<?php

require '../vendor/autoload.php';

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
//require_once '../includes/navigation.php';


$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
$codeLang = $_SESSION['lang']['code'];

require_once("../includes/fct_security.php");

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
        $Logged = createName($fname, $lname, $codeLang);
    }
}
else {
    $Logged=false;
    if ($GLOBALS['company']['redirect']){
        Redirect::to('soon.php');
    }
    $_SESSION['goto'] = $_SERVER['REQUEST_URI'];
    Redirect::to('login.php');
}
?>
<script>
    function get_editordata() {
        $("#btnJoin").attr("disabled", true);
        return true;
    }
</script>
<!-- wrapper -->
<div id="wrapper">
    <?php echo display_navbar_short($Logged); ?>

    
<?php 
    if(isset($_GET['u']) && !empty($_GET['u'])){
        $event_id = $_GET['u'];
        insert_event_log($event_id,$uid);
        echo displayEvent($event_id);
    }else{
        Redirect::to('index.php');
    }  
?>
<!-- Modal start -->
        <?php echo displayModal(); ?>
        <?php echo displayModalJoin(); ?>

<!-- Modal end -->
<?php require_once '../includes/footer.php';  ?>

</div>
    <?php
        $jaxon = jaxon();
        echo $jaxon->getJs();
        echo $jaxon->getScript();
    ?>

    <script>
    $(function(){
    $('[rel="popover"]').popover({
        container: 'body',
        html: true,
        content: function () {
            var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
            return clone;
        }
    }).click(function(e) {
        e.preventDefault();
    });
    </script>

<?php require_once '../includes/html_footer.php'; // currently just the closing /body and /html ?>
