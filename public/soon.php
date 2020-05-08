<?php

require '../vendor/autoload.php';

require_once '../init.php';
require_once("../includes/fct_display.php");

require_once '../includes/header.php';


$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
$codeLang = $_SESSION['lang']['code'];

require_once("../includes/fct_security.php");
$Logged = FALSE;
if(isset($user) && $user->isLoggedIn()){
    $user->logout();
}
?>
<div id="wrapper">
<?php echo display_navbar($Logged); ?>
   <?php echo displaySectionHomeSoon();?>
    <?php require_once '../includes/footer.php';  ?>
</div>

<?php require_once '../includes/html_footer.php'; ?>
