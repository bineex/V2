<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';
//Ajax Librairy ----
// Get the core singleton object
// and the Response class

use Jaxon\Jaxon;
use Jaxon\Response\Response;

require_once '../init.php';
require_once("../includes/fct_pass.php");

$jaxon->processRequest();// Call the Jaxon processing engine

require_once '../includes/header.php';

require_once("../includes/fct_security.php");
if (ip_blocked()){
    $user->logout();
    Redirect::to('blocked.php');        
}

if(isset($_GET['key']) && isset($_GET['mail'])){
    $key = $_GET['key'];
    $email = $_GET['mail'];        
}
else {
    $key='';
    $email='';
}

$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
$codeLang = $_SESSION['lang']['code'];

$Logged=false;
if(isset($user) && $user->isLoggedIn()){
    $Logged=true;
    Redirect::to('index.php');
}
$request = "Mail: ".$email." - Key: ".$key." - Key: ".$key." - URL: ".$_SERVER['SERVER_NAME'];
file_put_contents('log/log_resetpass_'.date("y-n-j").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - [GET] '. $request.PHP_EOL, FILE_APPEND);

    
?>

        <!--Home section end-->
        <!--Afer hero navbar-->
         <!--Afer hero navbar-->
       <nav id="navbar-sticky" class="navbar navbar-light navbar-expand-lg header-fullscreen sticky-header navbar-static-top">
            <a class="navbar-brand" href="index.php">
                <img src="<?php echo $GLOBALS['company']['logo'] ?>" alt="logo">
            </a>
            <div class="navbar-header">
                <button type="button" class="navbar-toggler collapsed" data-toggle="collapse" data-target="#navbar-scroll" aria-expanded="false">
                    <i class="icon-menu"></i>
                </button>
            </div>
            <div id="navbar-scroll" class="navbar-collapse collapse">
                <ul class="nav navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link active" data-scroll href="/index.php#home"><?php echo $lang['NAV_HOME'] ; ?></a>
                    </li>
                    <li class="dropdown nav-item">
                        <span class="dropdown-toggle nav-link menu-drop-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="icon-user"></i><?php echo $lang['NAV_ACCOUNT']; ?>
                            <i class="ion-ios-arrow-down"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a href="<?php echo ('login.php');?>" class="dropdown-item"><?php echo $lang['SIGNIN_TEXT']; ?></a>
                            </li>
                            <li>
                                <a href="<?php echo ('signup.php');?>" class="dropdown-item"><?php echo $lang['SIGNUP_TEXT']; ?></a>
                            </li>
                        </ul>
                    </li>
                   <?php if ($GLOBALS['company']['language'] == 'multi'){  ?>
                    <li class="dropdown nav-item">
                        <span class="dropdown-toggle nav-link menu-drop-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="icon-earth"></i><?php echo $libLang ; ?>
                            <i class="ion-ios-arrow-down"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a href="<?php echo ('?lang=en');?>" class="dropdown-item">ENG</a>
                            </li>
                            <li>
                                <a href="<?php echo ('?lang=jp');?>" class="dropdown-item">JPN</a>
                            </li>
                        </ul>
                    </li>
                    <?php } ?>
                </ul>
            </div>
            <!--/.nav-collapse -->
            <!--/.container -->
        </nav>
        <!--/.after hero navbar-->
        <!--/.after hero navbar-->


<!--section Requests start-->
    <section id='pricing' class='pricing-wrapper pt-100 pb-70'>
            <div class='container'>
            <form id='form_account'>
                <div class='row'>
                    <div class='col-md-4 ml-auto mr-auto'>                       
                        <div class='price-box scrollReveal sr-scaleDown sr-ease-in-out-back'>
                            <h3 id='titleForm'><?php echo $lang['PASSWORD_RESET'] ; ?></h3>
                            <div id='contentForm'>
 
                        </div>
                        <!--price box-->
                    </div>
                    <!--price col-->
                </div>
            </form>
            </div>
            <!--container-->
        </section>



<!-- footers -->
<?php 
     echo $jaxon->getJs();
     echo $jaxon->getScript();
?>
    <script type="text/javascript">window.jaxon_initResetPass('<?php echo $email ?>','<?php echo $key ?>');</script>
    


<!-- Place any per-page javascript here -->


<?php require_once '../includes/html_footer.php'; // currently just the closing /body and /html ?>
