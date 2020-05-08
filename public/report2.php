<?php
require_once '../init.php';
if (!isset($GLOBALS['company']['report']) || !$GLOBALS['company']['report']){
        Redirect::to('index.php');
}
?>

<script type="text/javascript">app_id="5bf241d58a5344086aaa65d4";distribution_key="dist_2";</script>
<script type="text/javascript" src="https://loader.knack.com/5bf241d58a5344086aaa65d4/dist_2/knack.js">
</script><div id="knack-dist_2">Loading...</div>

<footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-5 margin-b-30 order-xs-3">
                    <span>&copy; Copyright 2018 - 2019. <?php echo $lang['COPYRIGHT']; ?></span>
                </div>
            </div>
        </div>
</footer>

