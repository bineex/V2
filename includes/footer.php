<!-- FOOTER -->
    <!--footer start-->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 margin-b-30 order-xs-3">
                    <span class="fs-14">&copy; Copyright 2018 - 2020. <a href='https://tpo.me/' target='_blank' rel='nofollow'>TPO, Inc</a>
                    <br><?php echo $lang['COPYRIGHT']; ?></span>
                </div>
                <!--copyright col-->
                <div class="col-sm-6 text-right">
                    <ul class="float-right m-0 list-inline mobile-block fs-14">
                            <li><a href='<?php echo "documents/".$GLOBALS['company']['id']."/".$GLOBALS['company']['privacy_tpo'] ?>' target='_blank' rel='nofollow'><?php echo $lang['PRIVACY_TPO']; ?></a></li>
                            <?php if ($GLOBALS['company']['t&c_doc']) {?>
                            <li>&bull;</li>
                            <li><a href='<?php echo "documents/".$GLOBALS['company']['id']."/".$GLOBALS['company']['t&c_doc'] ?>' target='_blank' rel='nofollow'><?php echo $lang['TERMS_CONDITIONS']; ?></a></li>
                            <?php } ?>
                            <?php if ($GLOBALS['company']['privacy_doc']) {?>
                            <li>&bull;</li>
                            <li><a href='<?php echo "documents/".$GLOBALS['company']['id']."/".$GLOBALS['company']['privacy_doc'] ?>' target='_blank' rel='nofollow'><?php echo $lang['PRIVACY']; ?></a></li>
                            <?php } ?>
                    </ul>
                </div>
                <!--footer nav col-->
            </div>
            <!--row-->
        </div>
        <!--container-->
    </footer>
<!--footer end-->