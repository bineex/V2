 <?php

require '../vendor/autoload.php';

require_once '../init.php';
require_once("../includes/fct_freshdesk.php");
require_once("../includes/fct_display.php");

require_once '../includes/header.php';
require_once("../includes/fct_security.php");


if(isset($_GET['rqt']) && !empty($_GET['rqt'])){
        $id_ticket = $_GET['rqt'];
        $id_company= getTicketCompany($id_ticket);
        $portal = get_server_name ($id_company);

        $goto = 'https://'.$portal.'/login.php?rqt='.$id_ticket;
        Redirect::to($goto);
} 
 else {
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    exit;
}
?>
<!-- wrapper -->
<div id="wrapper">


<?php require_once '../includes/footer.php';  ?>

</div>
   
<?php require_once '../includes/html_footer.php'; ?>