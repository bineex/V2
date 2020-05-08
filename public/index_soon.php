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
// The function called by the browser
require_once("../includes/fct_freshdesk.php");
$jaxon->processRequest();// Call the Jaxon processing engine




require_once '../includes/header.php';
require_once("../includes/fct_display.php");
//require_once '../includes/navigation.php';

//$user->notLoggedInRedirect($abs_us_root.$us_url_root.'/users/login.php');
/*
echo '$abs_us_root: '.$abs_us_root;
echo '<br>';
echo '$us_url_root: '.$us_url_root;
 * 
 */
$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
//print_r($lang);
if(isset($user) && $user->isLoggedIn()){
}
?>
       <!--pre-loader-->
        <!--Intro section-->
        <section id="home" class="hero dark fullscreen-hero" data-jarallax='{"speed": 0.4}' style='background-image: url(assets/images/cover.jpg)'>
            <div class="hero-overlay hero-gradient"></div>
            <div class="hero-parallax">
                <div class="hero-inner">
                    <div class="hero-content">
                        <div class="container text-center">
                            <div class="row">
                                <div class="col-lg-8 mr-auto ml-auto">
                                    <h2 class="text-large text-white">
                                        Coming soon...
                                    </h2>
                                </div>
                            </div>
                        </div>
                        <!--container-->
                    </div>
                    <!--hero content-->
                </div>
                <!--hero inner-->
            </div>

            <svg class="svg-abs svg-f-btm" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1920 140" style="margin-bottom: -9px; enable-background:new 0 0 1920 140;" xml:space="preserve">
            <path class="svg-white" d="M960,92.9C811.4,93.3,662.8,89.4,515.3,79c-138.6-9.8-277.1-26.2-409-53.3C97.8,24,0,6.5,0,0c0,0,0,140,0,140
                  l960-1.2l960,1.2c0,0,0-140,0-140c0,2.7-42.1,11.3-45.8,12.2c-45.1,11-91.5,20.1-138.4,28.1c-176.2,30.1-359.9,43.8-542.9,48.9
                  C1115.4,91.4,1037.7,92.7,960,92.9z"></path>
            </svg>
            <!--parallax hero-->
            
        </section>
        <!--Home section end-->
        
<!-- Place any per-page javascript here -->
<?php
    echo $jaxon->getJs();
    echo $jaxon->getScript();
?>

<?php require_once '../includes/html_footer.php'; // currently just the closing /body and /html ?>
