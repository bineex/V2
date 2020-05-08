<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../../vendor/autoload.php';

require_once '../init.php';
require_once '../includes/header.php';

$error_message="";

$idLang = $_SESSION['lang']['id'];
$libLang = $_SESSION['lang']['lib'];
$codeLang = $_SESSION['lang']['code'];

?>

    <!--Home section end-->
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


<!--section Requests start-->
    <section id='pricing' class='pricing-wrapper pt-100 pb-70'>
            <div class='container text-center'>
                <div class='row'>
                    <div class='col-md-8 ml-auto mr-auto'>
                        <?php if(!$error_message=='') {?><div class="alert alert-danger">$error_message;?></div><?php } ?>
                        <div class='price-box scrollReveal sr-scaleDown sr-ease-in-out-back'>
                            <h6><?php echo $lang['BLOCKED_IP_TEXT']; ?></h6>
                            <div class="buttons scroll-to">
                                <a data-scroll href="index.php" class="btn btn-skin-border mr-2 mb-2 btn-lg"><?php echo $lang['NAV_HOME']?></a>
                            </div>
                        </div>
                        
                        <!--price box-->
                    </div>
                    <!--price col-->
                </div>
            </div>
            <!--container-->
    </section>

<!-- footers -->
<?php require_once '../includes/html_footer.php'; // currently just the closing /body and /html ?>
