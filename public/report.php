<?php

require '../vendor/autoload.php';

//Ajax Librairy ----
// Get the core singleton object
// and the Response class

use Jaxon\Jaxon;
use Jaxon\Response\Response;

require_once '../init.php';


require_once '../includes/header.php';
//require_once '../includes/navigation.php';

if ($GLOBALS['company']['redirect']){
        Redirect::to('index_soon.php');
}
if (!isset($GLOBALS['company']['report']) || !$GLOBALS['company']['report']){
        Redirect::to('index.php');
}

?>
<!-- wrapper -->

<script type="text/javascript">app_id="5bf241d58a5344086aaa65d4";distribution_key="dist_2";</script>
<script type="text/javascript" src="https://loader.knack.com/5bf241d58a5344086aaa65d4/dist_2/knack.js">
</script><div id="knack-dist_2">Loading...</div>

<?php require_once '../includes/footer.php';  ?>



<?php require_once '../includes/html_footer.php'; ?>
