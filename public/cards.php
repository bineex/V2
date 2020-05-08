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
    //$_SESSION['goto'] = $_SERVER['REQUEST_URI'];
    //Redirect::to('login.php');
}
?>
<!-- wrapper -->
<div id="wrapper">
<?php 
    if ($Logged){echo display_navbar($Logged);}
    
    if(isset($_GET['crd']) && !empty($_GET['crd'])){
        $card_id = $_GET['crd'];
        echo displayCard($card_id);
    }
    elseif ($GLOBALS['company']['redirect']){
        Redirect::to('index_soon.php');
    
    }
?>

<?php require_once '../includes/footer.php';  ?>

</div>


<?php require_once '../includes/html_footer.php'; // currently just the closing /body and /html ?>
