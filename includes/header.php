<?php
ob_start();
header('X-Frame-Options: SAMEORIGIN');

?>
<?php require_once '../helpers/helpers.php'; ?>

<?php
//check for a custom page
$currentPage = currentPage();

if(isset($_GET['err'])){
	$err = Input::get('err');
}

if(isset($_GET['msg'])){
	$msg = Input::get('msg');
}

$db = DB::getInstance();
$settingsQ = $db->query("Select * FROM settings");
$settings = $settingsQ->first();

//dealing with logged in users
if($user->isLoggedIn() && !checkMenu(2,$user->data()->id)){
	if (($settings->site_offline==1) && (!in_array($user->data()->id, $master_account)) && ($currentPage != 'login.php') && ($currentPage != 'maintenance.php')){
		//:: force logout then redirect to maint.page
		logger($user->data()->id,"Offline","Landed on Maintenance Page."); //Lggger
		$user->logout();
		Redirect::to($us_url_root.'users/maintenance.php');
	}
}

//deal with guests
if(!$user->isLoggedIn()){
	if (($settings->site_offline==1) && ($currentPage != 'login.php') && ($currentPage != 'maintenance.php')){
		//:: redirect to maint.page
		logger(1,"Offline","Guest Landed on Maintenance Page."); //Logger
		Redirect::to($us_url_root.'users/maintenance.php');
	}
}

//notifiy master_account that the site is offline
if($user->isLoggedIn()){
	if (($settings->site_offline==1) && (in_array($user->data()->id, $master_account)) && ($currentPage != 'login.php') && ($currentPage != 'maintenance.php')){
		err("<br>Maintenance Mode Active");
	}
}

if($settings->glogin==1 && !$user->isLoggedIn()){
	require_once $abs_us_root.$us_url_root.'users/includes/google_oauth.php';
}

if ($settings->force_ssl==1){

	if (!isset($_SERVER['HTTPS']) || !$_SERVER['HTTPS']) {
		// if request is not secure, redirect to secure url
		$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		Redirect::to($url);
		exit;
	}
}
require_once '../includes/security_headers.php';

//if track_guest enabled AND there is a user logged in
if($settings->track_guest == 1 && $user->isLoggedIn()){
	if ($user->isLoggedIn()){
		$user_id=$user->data()->id;
	}else{
		$user_id=0;
	}
	new_user_online($user_id);

}
//force password change
if($user->isLoggedIn() && $currentPage != 'user_settings.php' && $user->data()->force_pr == 1) Redirect::to($us_url_root.'users/user_settings.php?err=You+must+change+your+password!');

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <?php echo $script_analytics ?>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <title>Your Concierge</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="コンシェルジュが企業に常駐し、ワーカーのプライベート充実のためのサポートを行う、日本初の「コーポレート・コンシェルジュ」サービスを提供しています。YourConcierge for New Happiness">
	<meta name="keywords" content="TPO,corporate concierge,コーポレートコンシェルジュ,マニヤン麻里子">

	<!-- facebook -->
	<meta property="og:title" content="YourConcierge for New Happiness">
	<meta property="og:site_name" content="">
	<meta property="og:description" content="コンシェルジュが企業に常駐し、ワーカーのプライベート充実のためのサポートを行う、日本初の「コーポレート・コンシェルジュ」サービスを提供しています。YourConcierge for New Happiness">
	<meta property="og:url" content="https://myaccount.yourconcierge.jp">
	<meta property="og:image" content="https://myaccount.yourconcierge.jp/images/info/info1.jpg">
	<meta property="og:type" content="website">
	<!-- twitter -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="YourConcierge for New Happiness">
	<meta name="twitter:description" content="コンシェルジュが企業に常駐し、ワーカーのプライベート充実のためのサポートを行う、日本初の「コーポレート・コンシェルジュ」サービスを提供しています。YourConcierge for New Happiness">
	<meta name="twitter:image" content="https://myaccount.yourconcierge.jp/images/info/info1.jpg">
    <!-- mobile settings -->
    <meta name="viewport" content="width=device-width, maximum-scale=1, initial-scale=1, user-scalable=0" />
	<meta name="mobile-web-app-capable" content="yes">
    <!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->

    <!-- WEB FONTS : use %7C instead of | (pipe) -->
        <link href="https://fonts.googleapis.com/earlyaccess/notosansjapanese.css" rel="stylesheet" type="text/css" />
		

        <!-- CORE CSS -->
        <link href="assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />

        <!-- THEME CSS -->
        <link href="assets/css/essentials.css?4" rel="stylesheet" type="text/css" />
        <link href="assets/css/layout.css?2" rel="stylesheet" type="text/css" />
        
        <!-- PAGE LEVEL SCRIPTS -->
        <link href="assets/css/header-1.css?2" rel="stylesheet" type="text/css" />
        <link href="assets/css/color_scheme/<?php echo $theme_color ?>.css?4" rel="stylesheet" type="text/css" id="color_scheme" />
        <link href="assets/css/layout-datatables.css" rel="stylesheet" type="text/css" />

		<!-- SWIPER SLIDER -->
		<link href="assets/plugins/slider.swiper/dist/css/swiper.min.css" rel="stylesheet" type="text/css" />
        
        
</head>
    <body class="smoothscroll enable-animation">

	