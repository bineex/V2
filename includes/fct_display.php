  <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Jaxon\Jaxon;
use Jaxon\Response\Response;
require_once ('fct_admin_event.php');
require_once ('fct_freshdesk.php');
require_once ('fct_account.php');
require_once ('class_articles.php');
//Ajax Librairy ----
// and the Response class
// Get the core singleton object

$jaxon = jaxon();                        // Get the core singleton object   
$jaxon->register(Jaxon::USER_FUNCTION, 'displaySectionAbout'); // Register the function with Jaxon
$jaxon->register(Jaxon::USER_FUNCTION, 'displayWelcomeEvent');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayWelcome');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayNewsDetail');
$jaxon->register(Jaxon::USER_FUNCTION, 'joinEvent');
$jaxon->register(Jaxon::USER_FUNCTION, 'joinEventDirect');
$jaxon->register(Jaxon::USER_FUNCTION, 'initStripe');
$jaxon->register(Jaxon::USER_FUNCTION, 'leaveEvent');
$jaxon->register(Jaxon::USER_FUNCTION, 'alertUnlogged');
$jaxon->register(Jaxon::USER_FUNCTION, 'displayRequestFormType');
$jaxon->register(Jaxon::USER_FUNCTION, 'createGuestForm');
$jaxon->register(Jaxon::USER_FUNCTION, 'addGuestToList');

$company = $GLOBALS['company'];

function formatDateJP($date){
    Global $lang;

    if ($lang['CODE'] == 'ja-JP'){
      $fullFormatterjp = new IntlDateFormatter(
        'ja_JP',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE
      );
      return datefmt_format( $fullFormatterjp , strtotime($date));
    } else {
        $fullFormatterjp = new IntlDateFormatter(
            'en_US',
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            NULL,
            IntlDateFormatter::GREGORIAN  ,"EEE, MMM d"
        );
        //return datefmt_format( $fullFormatterjp , strtotime($date));
        return $fullFormatterjp->format(strtotime($date));
    }
}

function isBrowserIE(){
    if(strpos($_SERVER['HTTP_USER_AGENT'],'Trident'))
    {
        return true;
    }else{
        return false;
    }
    
}
function alertUnlogged () {
    Global $lang;
    $msg= $lang['ALERT_UNLOGGED'];
    
    $response = new Response();
    $response->alert($msg);
    $response->redirect('login.php');
    return $response;
    
}
function display_navbar($nameUser = FALSE, $permissions = -1){
    Global $lang;
    $company = $GLOBALS['company'];
    $display= '<div id="header" class="navbar-toggleable-md static clearfix">
         <!-- TOP NAV -->
        <header id="topNav">
            <div class="container">

                    <!-- Mobile Menu Button -->
                    <button class="btn btn-mobile" data-toggle="collapse" data-target=".nav-main-collapse">
                            <i class="fa fa-bars"></i>
                    </button>
            
                    <!-- Logo -->
                    <a class="logo float-left" href="index.php">
                       <img src="'.$GLOBALS['company']['logo'].'" alt="logo">
                    </a>';
                if ($permissions > -1){
                    $display .= '<div class="navbar-collapse collapse float-right nav-main-collapse submenu-dark"> 
                    <nav class="nav-main">
                    <ul id="topMain" class="nav nav-pills nav-main nav-onepage">';

                    switch ($company['id']) {
                        case '35001064691':
                            if ($nameUser){
                                $display .= '<li><a href="index.php#requests">'.$lang['NAV_REQUESTS'].'</a></li>';
                                $display .= '<li><a href="index.php#blog">'.$lang['NAV_NEWS'].'</a></li>';        
                                $display .= '<li><a href="index.php#events">'.$lang['NAV_PROGRAMS'].'</a></li>';
                                $display .= '<li><a href="index.php#howto_signup">'.$lang['NAV_HOWTO_SIGNUP'].'</a></li>';
                                $display .= '<li><a href="index.php#faq">'.$lang['NAV_FAQ'].'</a></li>';
                                $display .= '<li><a href="index.php#contact">'.$lang['NAV_CONTACT'].'</a></li>';
                            }
                            else{
                                $display .= '<li><a href="index.php#blog">'.$lang['NAV_NEWS'].'</a></li>';
                                $display .= '<li><a href="index.php#programs">'.$lang['NAV_PROGRAMS'].'</a></li>';                                
                                $display .= '<li><a href="index.php#howto_signup">'.$lang['NAV_HOWTO_SIGNUP'].'</a></li>';
                                $display .= '<li><a href="index.php#price">'.$lang['NAV_PRICE'].'</a></li>';
                                $display .= '<li><a href="index.php#contact">'.$lang['NAV_CONTACT'].'</a></li>';
                            }                            
                            break;
                        case '35001261629':
                            $display .= '';
                            $display .= '<li><a href="index.php#contact">'.$lang['NAV_CONTACT'].'</a></li>';            
                            break;
                        
                        default:
                            if ($nameUser){
                                $display .= '<li><a href="index.php#requests">'.$lang['NAV_REQUESTS'].'</a></li>';                            
                            }
                            if ($GLOBALS['company']['sections']['news']){
                                $display .= '<li><a href="index.php#blog">'.$lang['NAV_NEWS'].'</a></li>';
                            }                        
                            if ($GLOBALS['company']['sections']['event']){
                                $display .= '<li><a href="index.php#events">'.$lang['NAV_EVENT'].'</a></li>';
                            }
                            if ($GLOBALS['company']['sections']['how_to']){
                                $display .= '<li><a href="index.php#howto">'.$lang['NAV_HOWTO'].'</a></li>';
                            }                       
                            if ($GLOBALS['company']['sections']['about']) {
                                $display .= '<li><a href="index.php#about">'.$lang['NAV_ABOUT'].'</a></li>';           
                            }
                            if ($GLOBALS['company']['sections']['example']) {
                                $display .= '<li><a href="index.php#example">'.$lang['NAV_EXAMPLE'].'</a></li>';           
                            }
                            if ($GLOBALS['company']['sections']['team']) {
                                $display .= '<li><a href="index.php#team">'.$lang['NAV_TEAM'].'</a></li>';
                            }
                            if ($nameUser && $GLOBALS['company']['sections']['faq']){
                                $display .= '<li><a href="index.php#faq">'.$lang['NAV_FAQ'].'</a></li>';
                            }
                            break;
                    }
                    if ($nameUser){
                        $display .= '<li class="dropdown">
                            <a class="dropdown-toggle"  href="#">
                                <i class="icon-user"></i>'.$nameUser." 様".'
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="profile.php">'.$lang['NAV_PROFILE'].'</a></li>';
                                if ($permissions == 2){
                                    $display .= '<li><a href="admin.php">Admin</a></li>';
                                }
                                if (isset($company['subscription_id']) && $company['subscription_id']){
                                    $display .= '<li><a href="javascript:jaxon_displaySubscription();">'.$lang['NAV_SUBSCRIPTION'].'</a></li>';
                                }
                                if (isset($company['sponsorship_request']) && $company['sponsorship_request']){
                                    $display .= '<li><a href="javascript:jaxon_displayFormSponsorshipRequest();">Family request</a></li>';
                                }
                                $display .= '<li><a href="logout.php">'.$lang['SIGNOUT_TEXT'].'</a></li>
                            </ul>               
                        </li>';
                    }else {
                        $display .= '<li class="dropdown">
                            <a class="dropdown-toggle" href="#">'.$lang['NAV_ACCOUNT'].'</a>
                            <ul class="dropdown-menu">
                                <li><a href="login.php">'.$lang['SIGNIN_TEXT'].'</a></li>
                                <li><a href="signup.php">'.$lang['SIGNUP_TEXT'].'</a></li>
                            </ul>                        
                        </li>';
                    }
                    if ($GLOBALS['company']['language'] == 'multi'){
                        $display .= ' <li class="dropdown">                            
                            <a class="dropdown-toggle" href="#">'.$lang['LANGUAGE'].'</a>
                            <ul class="dropdown-menu">
                                <li><a href="index.php?lang=en">ENG</a></li>
                                <li><a href="index.php?lang=jp">JPN</a></li>
                            </ul>
                        </li>';
                    }
                    $display.='</ul>

                        </nav>
                    </div>';
                }
    $display .= '</div>
        </header>

    </div>';
    return $display;
}
function display_navbar_short($nameUser = FALSE, $permissions = -1){
    Global $lang;
    
    $display= '<div id="header" class="navbar-toggleable-md static clearfix">

         <!-- TOP NAV -->
        <header id="topNav">
                <div class="container">

                    <!-- Mobile Menu Button -->
                    <button class="btn btn-mobile" data-toggle="collapse" data-target=".nav-main-collapse">
                            <i class="fa fa-bars"></i>
                    </button>
            
                    <!-- Logo -->
                    <a class="logo float-left" href="index.php">
                       <img src="'.$GLOBALS['company']['logo'].'" alt="logo">
                    </a>';
                  
                    $display .= '<div class="navbar-collapse collapse float-right nav-main-collapse submenu-dark"> 

                        <nav class="nav-main">

                        <ul id="topMain" class="nav nav-pills nav-main nav-onepage">';

                    
                    $display .= '<li><a href="index.php">'.$lang['NAV_HOME'].'</a></li>';                 

                    if ($nameUser){
                        $display .= '<li class="dropdown">
                            <a class="dropdown-toggle"  href="#">
                                <i class="icon-user"></i>'.$nameUser." 様".'
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="profile.php">'.$lang['NAV_PROFILE'].'</a></li>';
                                if ($permissions == 2){
                                    $display .= '<li><a href="admin.php">Admin</a></li>';
                                }
                                $display .= '<li><a href="logout.php">'.$lang['SIGNOUT_TEXT'].'</a></li>
                            </ul>               
                        </li>';
                    }else {
                        $display .= '<li class="dropdown">
                            <a class="dropdown-toggle" href="#">'.$lang['NAV_ACCOUNT'].'</a>
                            <ul class="dropdown-menu">
                                <li><a href="login.php">'.$lang['SIGNIN_TEXT'].'</a></li>
                                <li><a href="signup.php">'.$lang['SIGNUP_TEXT'].'</a></li>
                            </ul>                        
                        </li>';
                    }
                    if ($GLOBALS['company']['language'] == 'multi'){
                        $display .= ' <li class="dropdown">                            
                            <a class="dropdown-toggle" href="#">'.$lang['LANGUAGE'].'</a>
                            <ul class="dropdown-menu">
                                <li><a href="index.php?lang=en">ENG</a></li>
                                <li><a href="index.php?lang=jp">JPN</a></li>
                            </ul>
                        </li>';
                    }

        $display .= '</ul>

                        </nav>
                    </div>';
                  
    $display .= '</div>
        </header>

    </div>';
    return $display;
}

function SectionTitle($title,$subtitle){
    
    $display='<header class="text-center mb-60 clearfix">
                <h2 class="fw-300 mb-0"><strong>'.$title.'</strong><br>'.$subtitle.'</h2>
            </header>';
    $display='<header class="text-center mb-60">
            <h2>'.$title.'</h2>
            <p class="lead">'.$subtitle.'</p>
            <hr />
    </header>';
    
    return $display;
  
}
function formatTags($arrayTags){
    $list ='<div class="hidden-xs-down mb-60">';
    foreach ($arrayTags as $key => $value){
        
        $libTag = $value->tag;
        $nbTag = $value->nb;
        $list .=  '<a class="tag" href="#">
                        <span class="txt">'.$libTag.'</span>
                        <span class="num">'.$nbTag.'</span>
                </a>';
    }
    $list .=  '</div>';
    return $list;
}
function showSearch(){
    $content='<!-- INLINE SEARCH -->
            <div class="inline-search clearfix mb-30">
                    <form action="#" method="get" class="widget_search">
                            <input type="search" placeholder="Start Searching..." id="s" name="s" class="serch-input">
                            <button type="submit">
                                    <i class="fa fa-search"></i>
                            </button>
                    </form>
            </div>
            <!-- /INLINE SEARCH -->

            <hr />';
    return $content;
}

function showEventDates($arrayDates){
    $booked = FALSE; Global $lang;
    
    $contentDates = '<div class="side-nav mb-60">
                    <div class="side-nav-head">
                            <button class="fa fa-bars"></button>
                            <h4>'.$lang['EVENT_SELECT_JOIN'].'</h4>
                    </div>
                    <ul class="list-group list-group-bordered list-group-noicon uppercase">';
                    foreach ($arrayDates as $date => $timeline){
        
                        if (strtotime ($date) >= strtotime (date('Y-m-d'))){
                            $contentDates .= '<li><h5><i class="fa fa-calendar-o"></i> '.formatDateJP($date).'</h5></li>';
                            foreach ($timeline as $id => $value){
                                if ($value['user_booked']){                                                
                                    $booked.='<a href="#" onclick="jaxon_leaveEvent('.$id.'); return false;"><li><h5>'.$date.': '.$value['start'].' - '.$value['end'].' <span class="badge badge-danger">'.$lang['BUTTON_UNSUBSCRIBE'].'</span></h5></li></a>';
                                }else{
                                    $qty_available = $value['quantity'] - $value['qty_booked'];
                                    $datetime = $date." ".$value['start'];
                                    //strtotime (date('Y-m-d'))
                                    if (strtotime ($datetime) < time()){
                                        $qty="-"; 
                                        $badge = "badge-light";                                                
                                        $contentDates.='<li><h5>'.$value['start'].' - '.$value['end'].' <span class="badge '.$badge.'">'.$qty.'</span></h5></li>';
                                    } elseif (intval($qty_available) == 0){
                                        $qty="0"; 
                                        $badge = "badge-danger";
                                        $contentDates.='<li><h5>'.$value['start'].' - '.$value['end'].' <span class="badge '.$badge.'">'.$qty.'</span></h5></li>';
                                    } elseif (intval($qty_available) < 0){
                                        
                                    } else {
                                        $qty = intval($qty_available); 
                                        $badge = "badge-success";
                                        $contentDates.='<li><a href="#" onclick="jaxon_joinEvent('.$id.'); return false;"><h5>'.$value['start'].' - '.$value['end'].' <span class="badge '.$badge.'">'.$qty.'</span></h5></a></li>';
                                    }
                                }
                                
                            }
                        }
                    }
    $contentDates .= '</ul></div>';
    if ($booked){
        $contentBooked = '<div class="side-nav mb-60">
                            <div class="side-nav-head">
                                    <button class="fa fa-bars"></button>
                                    <h4>'.$lang['EVENT_BOOKED'].'</h4>
                            </div>
                            <ul>'.$booked.'</ul>
                        </div>';
        $content = $contentBooked.$contentDates;
    }
    else {        
        $content = $contentDates;
    }
    return $content;
}

function showEvent($arrayEvent){
    $content = '<h1 class="blog-post-title">'.$arrayEvent['title'].'</h1>
            <ul class="blog-post-info list-inline">                    
                    <li><i class="fa fa-tags"></i>'.$arrayEvent['tags'].'</li>
            </ul>
            
            <p class="dropcap">'.$arrayEvent['description'].'</p>
            <div class="divider divider-dotted"><!-- divider --></div>
        ';

    return $content;
}
function showPost($objPost){
    $content = '<h1 class="blog-post-title">'.$objPost->title.'</h1>
            <ul class="blog-post-info list-inline">                    
                    <li>
                            <i class="fa fa-tags"></i>
                                    <span class="font-lato">'.$objPost->tags.'</span>
                    </li>
            </ul>

            <p class="dropcap">'.$objPost->description.'</p>
            <div class="divider divider-dotted"><!-- divider --></div>
        ';
    //return print_r($objPost,true);
    return $content;
}
function showCard($objPost){
    $content = '<p class="dropcap">'.$objPost->description.'</p>
            <div class="divider divider-dotted"><!-- divider --></div>
        ';
    //return print_r($objPost,true);
    return $content;
}

function displayModal(){
    Global $lang;
    $display='<div class="modal fade bs-example-modal-lg" id="modalp" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
                <div class="modal-header">                    	
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title" id="modal-title">Large modal</h5>
                </div>                    
                <div class="modal-body">
                  <div id="contentModal"></div>
                </div>
                <div class="modal-footer" id="modal-footer">
                  <button type="button" class="btn btn-primary" data-dismiss="modal">'.$lang['MODAL_CLOSE'].'</button>
                </div>
          </div>
        </div>
      </div>';
    return $display;
   
}
function displayModalJoin(){
    $display='<div id="myModalEvent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalEventLabel" aria-hidden="true">
	<div class="modal-dialog">
        <form class="eventInsForm" id="eventAddAttd" onsubmit="return (get_editordata() && jaxon_addAttendees(jaxon.getFormValues(\'eventAddAttd\')))">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">				
                    <h4 class="modal-title" id="myModalEventLabel">Modal title</h4>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div id="small-modalEvent-content"></div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer" id="modalEvent-footer"></div>
            </div>
        </form>
	</div>
    </div>';
    return $display;
}
function displayModalJoinDirect(){
    $display='<div id="myModalEvent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalEventLabel" aria-hidden="true">
	<div class="modal-dialog">
        <form class="eventInsForm" id="eventAddAttd" onsubmit="return (jaxon_addAttendees(jaxon.getFormValues(\'eventAddAttd\'),\'TRUE\'))">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">				
                    <h4 class="modal-title" id="myModalEventLabel">Modal title</h4>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">
                    <div id="small-modalEvent-content"></div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer" id="modalEvent-footer"></div>
            </div>
        </form>
	</div>
    </div>';
    return $display;
}
function displayModalSignup(){
    $display='<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">				
				<h4 class="modal-title" id="myModalLabel">Modal title</h4>
			</div>

			<!-- Modal Body -->
			<div class="modal-body">
				<div id="modal-content"></div>
			</div>

			<!-- Modal Footer -->
			<div class="modal-footer" id="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary">OK</button>
			</div>

		</div>
	</div>
    </div>';
    return $display;
}
function initStripe(){
    $response = new Response();
    $url="https://js.stripe.com/v3/";
    $response->script('$.getScript("'.$url.'");$.getScript("stripe/stripe_script.js");');
    return $response;
}
function joinEvent($id){
    Global $user; Global $lang; Global $company;
    $response = new Response();

    $account = $user->data();
    $email=$account->email;
    $uid=$account->fd_id;

    $codeLang = $_SESSION['lang']['code'];
    
    $isAvailable  = checkEventAvailability($id,$uid);
    
    if(!$isAvailable){
        $response->alert($lang['EVENT_AVAILABILITY']);
        //$response->alert($isAvailable);
        $response->redirect($_SERVER['REQUEST_URI']);
        return $response;
    }
    if(!empty($company['event_subscription'])){
        $subscription = getSubscriptionDetail($uid,$company['subscription_id']);
        $customer_id = ($subscription ? $subscription->customer : FALSE);
        $subscription_id = ($subscription ? $subscription->subscription_id : FALSE);

        if(empty($customer_id)){

            $display = "<p>".$lang['SUBSCRIPTION_NEEDED']."</p>";
        
            $submitButton = "<button type='button' class='btn btn-default' data-dismiss='modal'>".$lang['BUTTON_CANCEL']."</button>
                <button type='button' onclick='jaxon_initStripe();return false;' id='btnJoin' name='btnJoin' class='btn btn-primary' >".$lang['BUTTON_SUBSCRIBE']."</button>";  
                
            unset($_SESSION['subscription']);
            $_SESSION['subscription']['email'] = $account->email;
            $_SESSION['subscription']['fd_id'] = $account->fd_id;
            $_SESSION['subscription']['user_id'] = $account->id;
            $_SESSION['subscription']['plan'] = $company['subscription_id'];
            $_SESSION['subscription']['url'] = "https://".$_SERVER['SERVER_NAME'];
            $_SESSION['subscription']['success_url'] = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."&sub_id={CHECKOUT_SESSION_ID}";
            $_SESSION['subscription']['cancel_url'] = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
            $_SESSION['subscription']['config'] = $company['stripe'];

            $response->assign('small-modalEvent-content', 'innerHTML', $display);    
            $response->assign('modalEvent-footer', 'innerHTML', $submitButton);
            $response->script('$("#myModalEventLabel").html("'.$lang['BUTTON_SUBSCRIBE'].'");$("#myModalEvent").modal({"show":true});');
            
            return $response;
        }
        else {
            $plan = $company['subscription_id'];
            $uid = $account->fd_id;
            //$sync = syncSubscription($subscription_id);
            $subscription_status = checkSubscriptionStatus($uid,$plan);
            if(empty($subscription_status)){
                $response->alert($lang['SUBSCRIPTION_STATUS_INVALID']);
                return $response;
            }
        }
    }
    $scheduleDetail = getScheduleDetailImg($id,$codeLang);
    $detail = $scheduleDetail[0];

    $payment = $detail->amount;
    if ($payment > 0){
        $customer = getSubscriptionCustomer($uid);

        unset($_SESSION['subscription']);
        $_SESSION['subscription']['config'] = $company['stripe'];
        $_SESSION['subscription']['email'] = $account->email;
        $_SESSION['subscription']['fd_id'] = $account->fd_id;
        $_SESSION['subscription']['user_id'] = $account->id;
        $_SESSION['subscription']['customer'] = $customer;
        //$_SESSION['subscription']['name'] = $account->lname;
        $_SESSION['subscription']['images'] = $detail->img_url;
        $_SESSION['subscription']['description'] = formatDateJP($detail->date_start).': '.$detail->time_start.' - '.$detail->time_end;
        $_SESSION['subscription']['name'] = $detail->title;
        $_SESSION['subscription']['amount'] = $payment;
        $_SESSION['subscription']['id_schedule'] = $id;
        $_SESSION['subscription']['url'] = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."&sch=".$id;
        

        $url="https://js.stripe.com/v3/";
        $response->script('$.getScript("'.$url.'");$.getScript("stripe/stripe_script_payment.js");');
        return $response;
    }
    //$scheduleDetail = getScheduleDetail($id,$codeLang);
    //$detail = $scheduleDetail[0];

    $display = ' <ul class="list-group list-group-flush">                                            
                    <li class="list-group-item">
                        <i class="fa fa-calendar-o"></i> 
                        <span class="font-lato">'.formatDateJP($detail->date_start).'</span>                                               
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-clock-o"></i> 
                        <span class="font-lato">'.$detail->time_start.' '.$detail->time_end.'</span>                                           
                    </li>
                </ul>           
                <div class="invisible" >
                    <input type="text" id="user_id" name="user_id" value="'.$uid.'" />
                    <input type="text" id="schedule_id" name="schedule_id" value="'.$id.'" />
                </div>';
    
    $submitButton = "<button type='button' class='btn btn-default' data-dismiss='modal'>".$lang['BUTTON_CANCEL']."</button>
        <button type='submit' id='btnJoin' name='btnJoin' class='btn btn-primary' >".$lang['BUTTON_SUBSCRIBE']."</button>";  
    
    $response->assign('small-modalEvent-content', 'innerHTML', $display);    
    $response->assign('modalEvent-footer', 'innerHTML', $submitButton);
    $response->script('$("#myModalEventLabel").html("'.$detail->title.'");$("#myModalEvent").modal({"show":true});');
    
    return $response;
    
}
function joinEventDirect($id){
    Global $user; Global $lang; Global $company;
    $response = new Response();

    $account = $user->data();
    $email=$account->email;
    $uid=$account->fd_id;

    $codeLang = $_SESSION['lang']['code'];
    
    $scheduleDetail = getScheduleDetailImg($id,$codeLang);
    $detail = $scheduleDetail[0];

    $display = ' <ul class="list-group list-group-flush">                                            
                    <li class="list-group-item">
                        <i class="fa fa-calendar-o"></i> 
                        <span class="font-lato">'.formatDateJP($detail->date_start).'</span>                                               
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-clock-o"></i> 
                        <span class="font-lato">'.$detail->time_start.' '.$detail->time_end.'</span>                                           
                    </li>
                </ul>           
                <div class="invisible" >
                    <input type="text" id="user_id" name="user_id" value="'.$uid.'" />
                    <input type="text" id="schedule_id" name="schedule_id" value="'.$id.'" />
                </div>';
    
    $submitButton = "<button type='button' class='btn btn-default' data-dismiss='modal'>".$lang['BUTTON_CANCEL']."</button>
        <button type='submit' id='btnJoin' name='btnJoin' class='btn btn-primary' >".$lang['BUTTON_SUBSCRIBE']."</button>";  
    
    $response->assign('small-modalEvent-content', 'innerHTML', $display);    
    $response->assign('modalEvent-footer', 'innerHTML', $submitButton);
    $response->script('$("#myModalEventLabel").html("'.$detail->title.'");$("#myModalEvent").modal({"show":true});');
    
    return $response;
    
}
function leaveEvent($id){
   Global $user; Global $lang;
   
    $uid=$user->data()->fd_id;
    $codeLang = $_SESSION['lang']['code'];
    
    $scheduleDetail = getScheduleDetail($id,$codeLang);    
    $detail = $scheduleDetail[0];
    
    $display = ' <ul class="list-group list-group-flush">                                            
                    <li class="list-group-item">
                        <i class="fa fa-calendar-o"></i> 
                        <span class="font-lato">'.$detail->date_start.'</span>                                               
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-clock-o"></i> 
                        <span class="font-lato">'.$detail->time_start.' '.$detail->time_end.'</span>                                           
                    </li>
                </ul>';
    
    
    
    $submitButton = "<button type='button' class='btn btn-default' data-dismiss='modal'>".$lang['BUTTON_CANCEL']."</button>
        <button type='button' class='btn btn-primary' onclick='jaxon_deleteAttendees(".$id.",".$uid.");return false;'>".$lang['BUTTON_UNSUBSCRIBE']."</button>";  
    
    $response = new Response();   
    $response->assign('small-modalEvent-content', 'innerHTML', $display);
    $response->assign('modalEvent-footer', 'innerHTML', $submitButton);
    //$response->assign('modal-title', 'innerHTML', $id);
    $response->script('$("#myModalEventLabel").html("'.$detail->title.'");$("#myModalEvent").modal({"show":true});');
    return $response;
    
}

function displayEvent($event_id){
    Global $user; Global $lang;
  
    $uid=$user->data()->fd_id;
    
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    
    $title=$lang['SECTION_EVENT_TITLE'];
    $subtitle=$lang['SECTION_EVENT_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    $d_booked = array();
    
    $Articles= new articles();    
    $Eventf = $Articles->getEvent($event_id,$id_company,$codeLang);
    $Tags = $Articles->getTags(35000132685,$id_company,$codeLang); //35000132685 : Live EVENTS
    $d_booked = getListSchedulesBooked ($event_id,$uid);

    $isFirst = TRUE;
    //qty_booked
    foreach ($Eventf as $value) {
        if ($isFirst){
            $i = 0;
            $event['id'] = $value->article_id;
            $event['img_url'] = $value->img_url;
            $event['title'] = $value->title;
            $event['description']= $value->description;
            $event['amount']= $value->amount;
            $event['tags']= $value->tags;
            $event['schedule'][$value->date_start][$value->id_event_schedule]['start'] = $value->time_start;
            $event['schedule'][$value->date_start][$value->id_event_schedule]['end'] = $value->time_end;
            $event['schedule'][$value->date_start][$value->id_event_schedule]['quantity'] = $value->quantity;
            $event['schedule'][$value->date_start][$value->id_event_schedule]['quantity_max'] = $value->quantity_max;
            $event['schedule'][$value->date_start][$value->id_event_schedule]['qty_booked'] = $value->qty_booked;
            $isFirst = FALSE;
            
        } else{
            $event['schedule'][$value->date_start][$value->id_event_schedule]['start'] = $value->time_start;
            $event['schedule'][$value->date_start][$value->id_event_schedule]['end'] = $value->time_end;
            $event['schedule'][$value->date_start][$value->id_event_schedule]['quantity'] = $value->quantity;
            $event['schedule'][$value->date_start][$value->id_event_schedule]['quantity_max'] = $value->quantity_max;
            $event['schedule'][$value->date_start][$value->id_event_schedule]['qty_booked'] = $value->qty_booked;
        }
        $event['schedule'][$value->date_start][$value->id_event_schedule]['user_booked'] = in_array($value->id_event_schedule,$d_booked);
       
        $i++;
    }

    $d_event = showEvent($event);
    $d_timeline = showEventDates($event['schedule']);
    $d_tags = formatTags($Tags);
    $display = '<section>
            <div class="container">
                    <div class="row">
                            <!-- LEFT -->
                            <div class="col-md-9 col-sm-9">
                                '.$d_event.'
                            </div>
                            <!-- RIGHT -->
                            <div class="col-md-3 col-sm-3">
                                    <!-- side navigation -->
                                    <figure class="mb-20">
                                            <img class="img-fluid" src="'.$event['img_url'].'" alt="img" />
                                    </figure>
                                    '.$d_timeline.'
                                    <!-- TAGS -->
                                    <h3 class="hidden-xs-down fs-16 mb-20">TAGS</h3>
                                        '.$d_tags.'
                                    <hr />
                                    
                            </div>
                    </div>
            </div>
    </section>';
    //<h6 class="hidden-xs-down fs-16 mb-20">'.$event['amount'].'</h6>
    return $display;
}

function showPostListMini(){
    Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    
    $Articles= new articles();    
    $News = $Articles->getNews($id_company,$codeLang);

    foreach ($News as $value) {
    $display = '<div class="row tab-post">
                    <div class="col-md-3 col-sm-3 col-3">
                            <a href="blog-sidebar-left.html">
                                    <img src="'.$value->img_url.'" width="50" alt="" />
                            </a>
                    </div>
                    <div class="col-md-9 col-sm-9 col-9">
                            <a href="blog-sidebar-left.html" class="tab-post-link">'.$value->title.'</a>
                            <small>'.$value->created_at.'</small>
                    </div>    
                </div>';
    }
    return $display;
}


function displayExample($article_id){
    Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    
    $title=$lang['SECTION_EXAMPLE_TITLE'];
    $subtitle=$lang['SECTION_EXAMPLE_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $Articles= new articles();
    $Post = $Articles->getPost($article_id,$id_company,$codeLang);
    $Tags = $Articles->getTags(35000137386,$id_company,$codeLang); //35000132430 : Live Blogs
    
    $d_post = showPost($Post[0]);
    $d_tags = formatTags($Tags);
    
    $display = '<section>
            <div class="container">
                    <div class="row">
                            <!-- LEFT -->
                            <div class="col-md-9 col-sm-9">
                                '.$d_post.'
                            </div>
                            <!-- RIGHT -->
                            <div class="col-md-3 col-sm-3">
                                    <!-- side navigation -->
                                    <figure class="mb-20">
                                            <img class="img-fluid" src="'.$Post[0]->img_url.'" alt="img" />
                                    </figure>
                                    <!-- TAGS -->
                                    <h3 class="hidden-xs-down fs-16 mb-20">TAGS</h3>
                                        '.$d_tags.'
                                    <hr />
                                    <!-- TAGS -->
                            </div>
                    </div>
            </div>
    </section>';
    return $display;
}
function displayCard($article_id,$codeLang = NULL){
    
    $codeLang = ($codeLang == NULL) ? $_SESSION['lang']['code'] : $codeLang;

    //$codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    
    
    $Articles= new articles();
    $Post = $Articles->getPost($article_id,$id_company,$codeLang);
    $Tags = $Articles->getTags(35000135533,$id_company,$codeLang); //35000132430 : Cards
    
    $d_post = showCard($Post[0]);
    
    $display = '<section>
            <div class="container">
                <div class="col-md-9 col-sm-9 ml_auto mr_auto">
                    '.$d_post.'
                </div> 
            </div>
    </section>';
    return $display;
}
function displayContact(){
    Global $lang;
   
    $display='';
    
    if ($lang['CONTACTUS_1_TITLE']){
        
    $display ='<section id="contact">
            <div class="container">
                <div class="row">

                    <div class="col">
                            <div class="box-icon box-icon-center box-icon-round box-icon-transparent box-icon-large box-icon-content">
                                    <div class="box-icon-title">
                                            <i class="fa fa-map-marker"></i>
                                            <h2>'.$lang['CONTACTUS_1_TITLE'].'</h2>
                                    </div>
                                    <ul class="list-unstyled  text-center">'.$lang['CONTACTUS_1_BODY'].'</ul>
                                    </div>

                    </div>';
                            if ($lang['CONTACTUS_2_TITLE']){
                            $display.='
                            <div class="col">
                            <div class="box-icon box-icon-center box-icon-round box-icon-transparent box-icon-large box-icon-content">
                                    <div class="box-icon-title">
                                            <i class="fa fa-map-marker"></i>
                                            <h2>'.$lang['CONTACTUS_2_TITLE'].'</h2>
                                    </div>
                                    <ul class="list-unstyled text-center">'.$lang['CONTACTUS_2_BODY'].'</ul>
                                    </div>

                    </div>';
                            }

                    $display .='</div></div></section>';
    }
    return $display;
    
}
function displayBNews(){
    Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    
    $title=$lang['SECTION_NEWS_TITLE'];
    $subtitle=$lang['SECTION_NEWS_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $Articles= new articles();
    $News = $Articles->getBNews($id_company,$codeLang,4);
    
    $display = '<ul class="list-inline">
            <li class="fw-700 letter-spacing-1">News</li>';
    //data-original-title="" title="'.$value->title.'"
    foreach ($News as $value) {
            $display.='<li><a tabindex="0" href="#" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="bottom" data-content="'.$value->description_text.'" >'.$value->title.'</a></li>';
    }
    $display.='</ul>';
    return $display;
}
function displayBlog($article_id){
    Global $lang;
    $codeLang = $_SESSION['lang']['code']; 
    $id_company = $GLOBALS['company']['id'];
    
    $title=$lang['SECTION_EVENT_TITLE'];
    $subtitle=$lang['SECTION_EVENT_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $Articles= new articles();
    $Post = $Articles->getPost($article_id,$id_company,$codeLang);
    $Tags = $Articles->getTags(35000132430,$id_company,$codeLang); //35000132430 : Live Blogs
    
    $d_post = showPost($Post[0]);
    $d_tags = formatTags($Tags);
    //$d_posts = showPostListMini();
    
    $display = '<section>
            <div class="container">
                    <div class="row">
                            <!-- LEFT -->
                            <div class="col-md-9 col-sm-9">
                                '.$d_post.'
                            </div>
                            <!-- RIGHT -->
                            <div class="col-md-3 col-sm-3">
                                    <!-- side navigation -->
                                    <figure class="mb-20">
                                            <img class="img-fluid" src="'.$Post[0]->img_url.'" alt="img" />
                                    </figure>
                                    <!-- TAGS -->
                                    <h3 class="hidden-xs-down fs-16 mb-20">TAGS</h3>
                                        '.$d_tags.'
                                    <hr />                                    
                            </div>
                    </div>
            </div>
    </section>';
    return $display;

}

function displaySectionBlog(){
     Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    
    $title=$lang['SECTION_NEWS_TITLE'];
    $subtitle=$lang['SECTION_NEWS_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $Articles= new articles();    
    $News = $Articles->getNews($id_company,$codeLang);

    $display='<section id="blog" class="section-xs">
            <div class="container">'.$sectionTitle.'
                <div id="news" class="portfolio-gutter pajinate" data-pajinante-items-per-page="3" data-pajinate-container=".pajinate-container">
                    <div class="row pajinate-container">';
                foreach ($News as $value) {
                    $id=$value->article_id;
                    $display.=' <div class="col-md-4 col-sm-6">
                                    <div class="item-box">
                                        <a href="blog.php?u='.$id.'">
                                             <figure>
                                                <span class="item-hover"><span class="overlay dark-5"></span></span>
                                                <img class="img-fluid" src="'.$value->img_url.'" alt="">
                                            </figure>                                        
                                            <div class="item-box-desc text-center">                                            
                                                    <h5 class="text-center"><a href="blog.php?u='.$id.'">'.$value->title.'</a></h5>                                                                                               
                                            </div>
                                        </a>
                                    </div>
                                </div>';        
                }
            $display.='</div>
                <!-- Pagination Default -->
                    <div class="pajinate-nav">
                            <ul class="pagination">
                                    <!-- pages added by pajinate plugin -->
                            </ul>
                    </div>
                    <!-- /Pagination Default -->
                </div>
            </div>
        </section>';
    return $display;
}
function displaySectionEvents(){
    Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    $nbItems = $GLOBALS['company']['sections']['event_items'];
    $title=$lang['SECTION_EVENT_TITLE'];
    $subtitle=$lang['SECTION_EVENT_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $Articles= new articles();    
    $Events = $Articles->getEvents($id_company,$codeLang);
    $EventsSorted = $Articles->getEventsSorted();
    
    $current="";
    $event = [];
        foreach ($Events as $value) {
            if ($current != $value->article_id){
                $i = 0;
                $current = $value->article_id;
                $event[$value->article_id]['img_url'] = $value->img_url;
                $event[$value->article_id]['title'] = $value->title;
                $event[$value->article_id]['end'] = $value->date_start;
                $event[$value->article_id]['schedule'][$i]['date'] = $value->date_start;
                $event[$value->article_id]['schedule'][$i]['quantity'] = intval($value->qty) - intval($value->qty_booked);
            } else{
                $event[$value->article_id]['end'] = MAX($event[$value->article_id]['end'],$value->date_start);
                $event[$value->article_id]['schedule'][$i]['date'] = $value->date_start;
                $event[$value->article_id]['schedule'][$i]['quantity'] = intval($value->qty) - intval($value->qty_booked);
            }
            $i++;
        }
    $display = '<section class="section-xs" id="events">
            <div class="container">'.$sectionTitle.'
                <div id="events" class="portfolio-gutter pajinate" data-pajinante-items-per-page="'.$nbItems.'" data-pajinate-container=".pajinate-container">
                    <div class="row pajinate-container">';
    
        
                //foreach ($event as $key => $item) {
                foreach ($EventsSorted as $es) {
                    $id = $es->id_event;
                    if (!isset($event[$id])){
                        continue;
                    }
                    //$id = $key;
                    $item = $event[$id];
                    $display.=' <div class="col-md-4 col-sm-6">
                                    <div class="item-box">
                                    <a href="event.php?u='.$id.'">
                                        <figure>
                                                <span class="item-hover"><span class="overlay dark-5"></span></span>
                                                <img class="img-fluid" src="'.$item['img_url'].'" alt="">
                                        </figure>
                                        <div class="item-box-desc">
                                            <a href="event.php?u='.$id.'"><h3>'.$item['title'].'</h3></a>
                                            <br>
                                            <ul class="list-inline">';
                                            foreach ($item['schedule'] as $date) {
                                                if (strtotime ($date['date']) < strtotime (date('Y-m-d'))){
                                                    $qty="-"; 
                                                    $badge = "badge-light";
                                                    //$display.='<li><h6 class="text-muted">'.$date['date'].' <span class="badge '.$badge.'">'.$qty.'</span></h6></li>';
                                                } elseif (intval($date['quantity']) == 0){
                                                    $qty="0"; 
                                                    $badge = "badge-danger";
                                                    $display.='<li><h6 class="text-muted">'.formatDateJP($date['date']).' <span class="badge '.$badge.'">'.$qty.'</span></h6></li>';
                                                } elseif (intval($date['quantity']) < 0){                                                    
                                                } else {
                                                    $qty = intval($date['quantity']); 
                                                    $badge = "badge-success";
                                                    $display.='<li><h6>'.formatDateJP($date['date']).' <span class="badge '.$badge.'">'.$qty.'</span></h6></li>';
                                                }

                                            }
                    $display.='</ul></div></a></div></div>'; 
                }

            $display.='</div>
                <!-- Pagination Default -->
                    <div class="pajinate-nav">
                            <ul class="pagination">
                                    <!-- pages added by pajinate plugin -->
                            </ul>
                    </div>
                    <!-- /Pagination Default -->
            </div>
            </div>
        </section>';
    return $display;
}
function displaySectionEventsPayment(){
    Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    $nbItems = $GLOBALS['company']['sections']['event_items'];
    $title=$lang['SECTION_EVENTP_TITLE'];
    $subtitle=$lang['SECTION_EVENTP_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $Articles= new articles();    
    $Events = $Articles->getEventsPayment($id_company,$codeLang);
    $EventsSorted = $Articles->getEventsSorted();
    
    $current="";
    $event = [];
        foreach ($Events as $value) {
            if ($current != $value->article_id){
                $i = 0;
                $current = $value->article_id;
                $event[$value->article_id]['img_url'] = $value->img_url;
                $event[$value->article_id]['title'] = $value->title;
                $event[$value->article_id]['end'] = $value->date_start;
                $event[$value->article_id]['schedule'][$i]['date'] = $value->date_start;
                $event[$value->article_id]['schedule'][$i]['quantity'] = intval($value->qty) - intval($value->qty_booked);
            } else{
                $event[$value->article_id]['end'] = MAX($event[$value->article_id]['end'],$value->date_start);
                $event[$value->article_id]['schedule'][$i]['date'] = $value->date_start;
                $event[$value->article_id]['schedule'][$i]['quantity'] = intval($value->qty) - intval($value->qty_booked);
            }
            $i++;
        }
    $display = '<section class="section-xs" id="events">
            <div class="container">'.$sectionTitle.'
                <div id="events" class="portfolio-gutter pajinate" data-pajinante-items-per-page="'.$nbItems.'" data-pajinate-container=".pajinate-container">
                    <div class="row pajinate-container">';
    
        
                //foreach ($event as $key => $item) {
                foreach ($EventsSorted as $es) {
                    $id = $es->id_event;
                    if (!isset($event[$id])){
                        continue;
                    }
                    //$id = $key;
                    $item = $event[$id];
                    $display.=' <div class="col-md-4 col-sm-6">
                                    <div class="item-box">
                                    <a href="event.php?u='.$id.'">
                                        <figure>
                                                <span class="item-hover"><span class="overlay dark-5"></span></span>
                                                <img class="img-fluid" src="'.$item['img_url'].'" alt="">
                                        </figure>
                                        <div class="item-box-desc">
                                            <a href="event.php?u='.$id.'"><h3>'.$item['title'].'</h3></a>
                                            <br>
                                            <ul class="list-inline">';
                                            foreach ($item['schedule'] as $date) {
                                                if (strtotime ($date['date']) < strtotime (date('Y-m-d'))){
                                                    $qty="-"; 
                                                    $badge = "badge-light";
                                                    //$display.='<li><h6 class="text-muted">'.$date['date'].' <span class="badge '.$badge.'">'.$qty.'</span></h6></li>';
                                                } elseif (intval($date['quantity']) == 0){
                                                    $qty="0"; 
                                                    $badge = "badge-danger";
                                                    $display.='<li><h6 class="text-muted">'.formatDateJP($date['date']).' <span class="badge '.$badge.'">'.$qty.'</span></h6></li>';
                                                } elseif (intval($date['quantity']) < 0){                                                    
                                                } else {
                                                    $qty = intval($date['quantity']); 
                                                    $badge = "badge-success";
                                                    $display.='<li><h6>'.formatDateJP($date['date']).' <span class="badge '.$badge.'">'.$qty.'</span></h6></li>';
                                                }

                                            }
                    $display.='</ul></div></a></div></div>'; 
                }

            $display.='</div>
                <!-- Pagination Default -->
                    <div class="pajinate-nav">
                            <ul class="pagination">
                                    <!-- pages added by pajinate plugin -->
                            </ul>
                    </div>
                    <!-- /Pagination Default -->
            </div>
            </div>
        </section>';
    return $display;
}
function displaySectionWeekEvents(){
    Global $lang;Global $user;

    $account = $user->data();
    $user_id = $account->fd_id;

    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    $nbItems = 5;
    $title=$lang['SECTION_EVENT_TITLE'];
    $subtitle=$lang['SECTION_EVENT_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $Articles= new articles();     
    $Events = $Articles->getEventsbyDate($id_company,$codeLang);

    $firstday = date('Y-m-d', strtotime("this week"));

    $current="";
    $event = [];
    foreach ($Events as $value) {
            $current = $value->date_start;
            $event[$value->date_start][$value->id_event_schedule]['id_event'] = $value->id_event;
            $event[$value->date_start][$value->id_event_schedule]['title'] = $value->title;
            $event[$value->date_start][$value->id_event_schedule]['img_url'] = $value->img_url;
            $event[$value->date_start][$value->id_event_schedule]['id_schedule'] = $value->id_event_schedule;
            $event[$value->date_start][$value->id_event_schedule]['time_start'] = $value->time_start;
            $event[$value->date_start][$value->id_event_schedule]['time_end'] = $value->time_end;
        
    }
    $display = '<section class="section-xs" id="events">
            <div class="container">'.$sectionTitle.'
                <div id="events" class="portfolio-gutter pajinate" data-pajinante-items-per-page="'.$nbItems.'" data-pajinate-container=".pajinate-container">
                    <!-- Pagination Default -->
                    <div class="pajinate-nav">
                            <ul class="pagination">
                                    <!-- pages added by pajinate plugin -->
                            </ul>
                    </div>
                    <!-- /Pagination Default -->
                    <div class="row pajinate-container ">';

                    $currentday = $firstday;
                for ($w = 1; $w <= 4; $w++) {
                    $boxstyle = "";
                    for ($i = 1; $i <= 5; $i++) {
                        $boxstyle = ($i%2 ? "" : "box-transparent");
                        $boxstyle = (strtotime ($currentday) == strtotime (date('Y-m-d')) ? "box-color" : $boxstyle);
                        $display.=' <div class="p-2 col-md-5th col-sm-5th text-center box-static box-border-top '.$boxstyle.'">
                                        <h4>'.formatDateJP($currentday).'</h4>';

                        if (isset($event[$currentday])){
                            $eventsDay = $event[$currentday];
                            foreach ($eventsDay as $schedule_id => $values){
                                $display.='<div class="price-clean">
                                    <a href="event.php?u='.$values['id_event'].'">
                                        <figure>
                                            <img class="img-fluid" src="'.$values['img_url'].'" alt="'.$values['title'].'">
                                        </figure>
                                    </a>
                                    <p class="h-70">'.$values['title'].'</p>
                                    <hr />
                                    <p>'.$values['time_start'].' - '.$values['time_end'].'</p>';
                                        
                                            
                                        
                                //if (strtotime ($currentday) == strtotime (date('Y-m-d')) && strtotime ($event[$currentday]['time_start']) > time()){
                                    $button='';
                                if ((strtotime ($currentday) > strtotime (date('Y-m-d'))) || (strtotime ($currentday) ==  strtotime (date('Y-m-d')) && strtotime ($values['time_end']) > time())){
                                
                                        if (isScheduleBooked($schedule_id,$user_id)){
                                            //$display.=' <a href="#" onclick="jaxon_joinEventDirect('.$event[$currentday]['id_schedule'].'); return false;" class="btn btn-outline-primary">View</a>';
                                            $button.='<a class="btn btn-primary" href="#" onclick="jaxon_displayEventDescription('.$values['id_event'].')">View</a>';
                                        } else {
                                            //$display.='<li><button type="button" class="btn btn-outline-primary" onclick="jaxon_joinEventDirect('.$schedule_id.')">'.$lang['BUTTON_SUBSCRIBE'].'</button></li>';
                                            $button.='<a class="btn btn-primary" href="#" onclick="jaxon_joinEventDirect('.$schedule_id.')">'.$lang['BUTTON_SUBSCRIBE'].'</a>';
                                        }
                                    
                                }
                                $display.= '<p class="h-50">'.$button.'</p></div>';
                            }
                        }
                        $display.='</div>';                        
                        $currentday = date("Y-m-d", strtotime($currentday.'+ 1 days'));
                        
                    }
                    $currentday = date("Y-m-d", strtotime($currentday.'+ 2 days'));
                }

            $display.='</div>
                <!-- Pagination Default -->
                    <div class="pajinate-nav">
                            <ul class="pagination">
                                    <!-- pages added by pajinate plugin -->
                            </ul>
                    </div>
                <!-- /Pagination Default -->
            </div>
            </div>
        </section>';
    return $display;
}
function displaySectionFAQ(){
    Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    
    $title=$lang['SECTION_FAQ_TITLE'];
    $subtitle=$lang['SECTION_FAQ_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $Articles= new articles();
    $faqs = $Articles->getFAQs($id_company,$codeLang);

    
    $display = '<section id="faq" class="alternate">
        
        <div class="container">'.$sectionTitle.'
        <div class="toggle toggle-transparent toggle-bordered-full toggle-accordion">';
    
    foreach ($faqs as $value) {
        $id=$value->article_id;
        $display .= '
            <div class="toggle">
		<label>'.$value->title.'</label>
		<div class="toggle-content">
			<p>'.$value->description_text.'</p>
		</div>
	</div>';
        }
  
  $display .= '</div>
            </div>
            </section>';
    return $display;
    
    
}
function displaySectionAbout(){
    Global $lang;
    $title=$lang['SECTION_ABOUT_TITLE'];
    $subtitle=$lang['SECTION_ABOUT_SUBTITLE'];
    $idLang = $_SESSION['lang']['id'];
        
    $sectionTitle=SectionTitle($title,$subtitle);
     $display ='<section id="about">
                    <div class="container">'.$sectionTitle.'
                        <div class="col">
                            <div class="item-box">
                                 <figure>
                                    <a class="ico-rounded lightbox" href="images/about/'.$lang['SECTION_ABOUT_IMAGE'].'" data-plugin-options=\'{"type":"image"}\'>
                                            <img class="img-fluid" src="images/about/'.$lang['SECTION_ABOUT_IMAGE'].'" alt="yourconcierge.jp" />
                                    </a>
                                </figure>
                            </div>
                        </div>
                    </div>
                </section>';
    return $display;
}
function displaySectionTeam(){
    Global $lang;
    $title=$lang['SECTION_TEAM_TITLE'];
    $subtitle=$lang['SECTION_TEAM_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    $display='<section id="team">
            <div class="container">'.$sectionTitle;             
                $i = 1;
                    While(isset($lang['TEAM_'.$i])){
                         $display.='<div class="row pb-4">';
                         $j = 1;
                          While($j<=3){
                            $display.='
                            <div class="col-md-4 col-sm-6">
                                <div class="box-flip box-color box-icon box-icon-center box-icon-round box-icon-large text-center">
                                    <div class="front">
                                        <div class="box1 box-default">
                                            <div class="box-icon-title">
                                                <img class="img-fluid" src="'.$lang["TEAM_".$i]["PHOTO"].'" alt="" />
                                                <h2>'.$lang["TEAM_".$i]["NAME"].'</h2>    
                                                <hr />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="back">
                                        <div class="box2 box-default">
                                            <h4 class="m-0">'.$lang["TEAM_".$i]["NAME"].'</h4>
                                            <hr />
                                            <p>'.$lang["TEAM_".$i]["BIO"].'</p>
                                            <hr />
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            $j++;$i++;
                            if (!isset($lang['TEAM_'.$i])){
                                break;
                            }
                          }
                        $display.='</div>';                         
                    }
                $display.='</div>
        </section>';
    return $display;
}
function displaySectionPrograms(){
    Global $lang;
    $title=$lang['SECTION_PROGRAMS_TITLE'];
    $subtitle=$lang['SECTION_PROGRAMS_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    $display='<section id="programs">
            <div class="container">'.$sectionTitle;             
                $i = 1;
                    While(isset($lang['PROG_'.$i])){
                         $display.='<div class="row pb-4">';
                         $j = 1;
                          While($j<=3){
                            $display.='
                            <div class="col-md-4 col-sm-6">
                                <div class="box-flip box-color box-icon box-icon-center box-icon-round box-icon-large text-center">
                                    <div class="front">
                                        <div class="box1 box-default">
                                            <div class="box-icon-title">
                                                <img class="img-fluid" src="'.$lang["PROG_".$i]["PHOTO"].'" alt="" />
                                                <h2>'.$lang["PROG_".$i]["NAME"].'</h2>    
                                                <hr />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="back">
                                        <div class="box2 box-default">
                                            <h4 class="m-0">'.$lang["PROG_".$i]["NAME"].'</h4>
                                            <hr />
                                            <p>'.$lang["PROG_".$i]["BIO"].'</p>
                                            <hr />
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            $j++;$i++;
                            if (!isset($lang['PROG_'.$i])){
                                break;
                            }
                          }
                        $display.='</div>';                         
                    }
                $display.='</div>
        </section>';
    return $display;
}
function displaySectionPrivates(){
    Global $lang;
    $title=$lang['SECTION_PRIVATES_TITLE'];
    $subtitle=$lang['SECTION_PRIVATES_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    $display='<section id="programs">
            <div class="container">'.$sectionTitle;             
                $i = 1;
                    While(isset($lang['SERV_'.$i])){
                         $display.='<div class="row pb-4">';
                         $j = 1;
                          While($j<=3){
                            $display.='
                            <div class="col-md-4 col-sm-6">
                                <div class="box-flip box-color box-icon box-icon-center box-icon-round box-icon-large text-center">
                                    <div class="front">
                                        <div class="box1 box-default">
                                            <div class="box-icon-title">
                                                <img class="img-fluid" src="'.$lang["SERV_".$i]["PHOTO"].'" alt="" />
                                                <h2>'.$lang["SERV_".$i]["NAME"].'</h2>    
                                                <hr />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="back">
                                        <div class="box2 box-default">
                                            <h4 class="m-0">'.$lang["SERV_".$i]["NAME"].'</h4>
                                            <hr />
                                            <p>'.$lang["SERV_".$i]["BIO"].'</p>
                                            <hr />
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            $j++;$i++;
                            if (!isset($lang['SERV_'.$i])){
                                break;
                            }
                          }
                        $display.='</div>';                         
                    }
                $display.='</div>
        </section>';
    return $display;
}
function displaySectionExample(){
     Global $lang;
    $codeLang = $_SESSION['lang']['code'];
    $id_company = $GLOBALS['company']['id'];
    
    $title=$lang['SECTION_EXAMPLE_TITLE'];
    $subtitle=$lang['SECTION_EXAMPLE_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $Articles= new articles();    
    $News = $Articles->getExample($id_company,$codeLang);

    $display='<section class="section-xs" id="example">
            <div class="container">'.$sectionTitle.'
                <div id="news" class="portfolio-gutter pajinate" data-pajinante-items-per-page="3" data-pajinate-container=".pajinate-container">
                    <div class="row pajinate-container">';
                foreach ($News as $value) {
                    $id=$value->article_id;
                    $display.=' <div class="col-md-4 col-sm-6">
                                    <div class="item-box">
                                        <a href="example.php?u='.$id.'">
                                             <figure>
                                                <span class="item-hover"><span class="overlay dark-5"></span></span>
                                                <img class="img-fluid" src="'.$value->img_url.'" alt="">
                                            </figure>                                        
                                            <div class="item-box-desc text-center">                                            
                                                    <h5 class="text-center"><a href="blog.php?u='.$id.'">'.$value->title.'</a></h5>                                  
                                            </div>
                                        </a>
                                    </div>
                                </div>';        
                }
            $display.='</div>
                <!-- Pagination Default -->
                    <div class="pajinate-nav">
                            <ul class="pagination">
                                    <!-- pages added by pajinate plugin -->
                            </ul>
                    </div>
                    <!-- /Pagination Default -->
                </div>
            </div>
        </section>';
    return $display;
}
function displaySectionHowto(){
     Global $lang;
     
    $title=$lang['SECTION_HOWTO_TITLE'];
    $subtitle=$lang['SECTION_HOWTO_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $display='<section id="howto">
            <div class="container"> 
            '.$sectionTitle;
    
             $i = 1;
            While($lang['HOWTO_'.$i.'_TITLE'] != NULL){
                 $display.='<div class="row">';
                 $j = 1;
                 While($j<=3){
                    $display .='<div class="col-md-12 col-sm-12 col-lg-4 margin-b-30">
                                    <div class="ico-light ico-lg ico-rounded ico-hover sr-delay-1">
                                        <div class="text-center">
                                            <div class="col-4 ml-auto mr-auto">
                                                <img src="'.$lang['HOWTO_'.$i.'_ICON'].'" class="img-fluid" alt="service-img">
                                            </div> 
                                            <h4>'.$lang['HOWTO_'.$i.'_TITLE'].'</h4>                                            
                                        </div>
                                        <p>'.$lang['HOWTO_'.$i.'_DESC'].'</p>
                                    </div>
                                </div>';
                    $j++;$i++;
                }
                $display.='</div>';
            }
            $display.='</div>
            </section>';
 
    return $display;
}
function displaySectionHowtoSignup(){
    Global $lang;
    
   $title=$lang['SECTION_HOWTO_SIGNUP_TITLE'];
   $subtitle=$lang['SECTION_HOWTO_SIGNUP_SUBTITLE'];
   
   $sectionTitle=SectionTitle($title,$subtitle);
   
   $display='<section id="howto_signup">
           <div class="container"> 
           '.$sectionTitle;
   
            $i = 1;
           While(isset($lang['HOWTOS_'.$i.'_TITLE'])){
                $display.='<div class="row">';
                $j = 1;
                While($j<=3){
                   $display .='<div class="col-md-12 col-sm-12 col-lg-4 margin-b-30">
                                   <div class="ico-light ico-lg ico-rounded ico-hover sr-delay-1">
                                       <div class="text-center">
                                           <div class="col-4 ml-auto mr-auto">
                                               <img src="'.$lang['HOWTOS_'.$i.'_ICON'].'" class="img-fluid" alt="service-img">
                                           </div> 
                                           <h4>'.$lang['HOWTOS_'.$i.'_TITLE'].'</h4>                                            
                                       </div>
                                       <p>'.$lang['HOWTOS_'.$i.'_DESC'].'</p>
                                   </div>
                               </div>';
                   $j++;$i++;
               }
               $display.='</div>';
           }
           $display.='</div>
           </section>';

   return $display;
}
function displaySectionPrice(){
    Global $lang;
    
   $title=$lang['SECTION_PRICE_TITLE'];
   $subtitle=$lang['SECTION_PRICE_SUBTITLE'];
   
   $sectionTitle=SectionTitle($title,$subtitle);
   
   $display='<section id="price">
           <div class="container"> 
           '.$sectionTitle;
   
            $i = 1;
           While(isset($lang['PRICE_'.$i.'_TITLE'])){
                $display.='<div class="row">';
                $j = 1;
                While($j<=3 && isset($lang['PRICE_'.$i.'_TITLE'])){
                   $display .='<div class="col-md-12 col-sm-12 col-lg-4 margin-b-30">
                                   <div class="ico-light ico-lg ico-rounded ico-hover sr-delay-1">
                                       <div class="text-center">
                                           <div class="col-4 ml-auto mr-auto">
                                               <img src="'.$lang['PRICE_'.$i.'_ICON'].'" class="img-fluid" alt="service-img">
                                           </div> 
                                           <h4>'.$lang['PRICE_'.$i.'_TITLE'].'</h4>                                            
                                       </div>
                                       <p>'.$lang['PRICE_'.$i.'_DESC'].'</p>
                                   </div>
                               </div>';
                   $j++;$i++;
               }
               $display.='</div>';
           }
           $display.='</div>
           </section>';

   return $display;
}
function displaySectionCallout(){
    Global $lang;
    
    $display ='<!-- CALLOUT -->
    <section class="theme-color section-xs" id="callout">
        <div class="container">
            <div class="row">
                <div class="col-md-9 theme-color">
                    <h5 class="fs-25"><strong>'.$lang['SECTION_CALLOUT_TITLE'].'</strong></h5>
                    <h5>'.$lang['SECTION_CALLOUT_SUBTITLE'].'</h5>
                </div>
                <div class="col-md-3 theme-color">
                    <a href="signup.php" class="btn btn-lg btn-primary wow fadeInUp" data-wow-delay="1s">'.$lang['SIGNUP_TEXT'].'</a>                        
                    <a href="login.php" class="btn btn-lg btn-primary wow fadeInUp" data-wow-delay="1s">'.$lang['SIGNIN_TEXT'].'</a>
                </div>
            </div>            
        </div>
    </section>
     <!-- /CALLOUT -->';
     /*
    <div class="divider divider-center divider-short"><i class="fa fa-calendar-o"></i></div>
    <div class="container">
        <div class="col">
            <div class="item-box">
                <figure>
                <a class="ico-rounded lightbox" href="images/calendar/calendar_'.$lang['LANGUAGE_ID'].'.jpg" data-plugin-options=\'{"type":"image"}\'>
                <img class="img-fluid" src="images/calendar/calendar_'.$lang['LANGUAGE_ID'].'.jpg" alt="calendar" />
                </a>
                </figure>
            </div>
        </div>
    </div>';
    */

    return $display;
}
function displaySectionCards(){
    Global $lang;
    $title='Cards';
    $subtitle='';
    
    $sectionTitle=SectionTitle($title,$subtitle);
    $display='<section id="team">
            <div class="container">'.$sectionTitle;
                $nbCards = 6;
                $i = 1;
                    While($i <= $nbCards){
                         $display.='<div class="row pb-4">';
                         $j = 1;
                          While($j<=3){
                            $display.='
                            <div class="col-md-4 col-sm-6">
                                <div class="box-flip">
                                    <div class="front">
                                        <div class="box1 box-white">
                                            <div class="box-icon-title">
                                                <img class="img-fluid" src="images/cards/cafe_front.png" alt="" />                                          
                                                <hr />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="back">
                                        <div class="box2 box-white">
                                            <img class="img-fluid" src="images/cards/cafe_back.png" alt="" />
                                            <hr />
                                        </div>
                                    </div>
                                </div>
                            </div>';
                            $j++;$i++;
                          }
                        $display.='</div>';                         
                    }
                $display.='</div>
        </section>';
    return $display;
}
function displaySectionRequests($user_id){
    Global $lang;
    $title=$lang['SECTION_REQUESTS_TITLE'];
    $subtitle=$lang['SECTION_REQUESTS_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);

    $freshTickets= new freshdesk();    
    $tabTickets = $freshTickets->getTicketList($user_id);

    $statusList[2] = 'Open';
    $statusList[3] = 'Pending';
    $statusList[4] = 'Resolved';
    $statusList[5] = 'Closed';
    $statusList[8] = 'Booked';
    $statusList[17] = 'Booked';
    $statusList[19] = 'Cancelled';
    
    $displayTickets = "<table id='datatable_requests' class='table table-sm table-hover'>
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>";

    foreach ($tabTickets as $key=>$values){
        if (intval($values['association_type']) < 2){
            $ticket = array(
                    'id' => $values['id'],
                    'user' => $user_id,
                    'subject' => $values['subject'],
                    'status' => $values['status']);

            //if ($ticket_id == $values['id']){$ticket_selected = $values['id'];}

            $date = new DateTime($values['created_at']);
            $date->setTimezone(new DateTimeZone('Asia/Tokyo'));
            $date_created = $date->format('Y-m-d H:i');

            $subject=$values['subject'];
            $display_id=$values['id'];

            $status='';
            if (isset($statusList[$values['status']])){
                $status = $statusList[$values['status']];
                if ($statusList[$values['status']] == 'Open' || $statusList[$values['status']] == 'Booked'){
                    $status = "<span class='badge badge-success'>".$statusList[$values['status']]."</span>";
                    //$subject= "<th scope='row' >$subject</th>";
                }
                elseif ($statusList[$values['status']] == 'Closed' || $statusList[$values['status']] == 'Cancelled' ){
                    $status = "<small class='text-muted'>".$statusList[$values['status']]."</small>";
                    //$subject= "<td scope='row' >$subject</td>";
                }
                //<h3><span class="badge badge-success">Label</span></h3>
            }
            else{
                $status = $values['status'];
            }
            //$jsonTicket = json_encode($ticket,JSON_HEX_APOS);
            $displayTickets .= "<tr class='odd gradeX' onclick='jaxon_displayTicket(".$values['id'].");return false;'>
              <td>$display_id</td>
              <td>$subject</td>
              <td>$date_created</td>
              <td>$status</td>

            </tr>";
        }
    }
    $displayTickets .= "</tbody></table>";

    $display='<section id="requests" class="pt-100 pb-100">
                <div class="container">'.$sectionTitle.'
                    <div class="row">
                        <div class="col-md-12">
                                <div class="panel panel-default">
                                        <div class="panel-heading"><strong></strong></div>
                                        <div class="panel-body">'.$displayTickets.'</div>
                                </div><!-- /panel -->
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div> <!-- /container -->
            </section>';
    return $display;
}
function displaySectionService(){
    Global $lang;
    
    $title=$lang['SECTION_SERVICE_TITLE'];
    $subtitle=$lang['SECTION_SERVICE_SUBTITLE'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    
    $display='<section id="services">
            <div class="container"> 
            '.$sectionTitle;
    
             $i = 1;
            While(isset($lang['SERVICE_'.$i.'_TITLE'])){
                 $display.='<div class="row">';
                 $j = 1;
                 While($j<=4){
                    $display .='<div class="col-md-12 col-sm-12 col-lg-3 margin-b-30">
                                    <div class="ico-light ico-lg ico-rounded ico-hover sr-delay-1">
                                        <div class="text-center">
                                            <div class="col-4 ml-auto mr-auto">
                                                <img src="'.$lang['SERVICE_'.$i.'_ICON'].'" class="img-fluid" alt="service-img">
                                            </div> 
                                            <h4>'.$lang['SERVICE_'.$i.'_TITLE'].'</h4>                                            
                                        </div>
                                        <p class="text-center">'.$lang['SERVICE_'.$i.'_DESC'].'</p>
                                    </div>
                                </div>';
                    $j++;$i++;
                }
                $display.='</div>';
            }
            $display.='</div>
            </section>';
 
    return $display;
}
function displaySectionInfo(){
    Global $lang;
    $title = $lang['SECTION_INFO_TITLE'];
   $subtitle = $lang['SECTION_INFO_DESC'];
    
    $sectionTitle=SectionTitle($title,$subtitle);
    $display='<!-- Info -->
    <section id="info">
        <div class="container">
            
            <div class="row">

                <div class="col-md-6 col-sm-6">
                    <img class="img-fluid wow fadeIn" data-wow-delay="0.1s" src="images/info/info1.jpg" alt="" />
                </div>

                <div class="col-md-6 col-sm-6">
                    <header class="mb-60">
                        <h2>'.$lang['SECTION_INFO_TITLE'].'</h2>
                        <p class="lead font-lato"></p>
                    </header>
                    '.$lang['SECTION_INFO_DESC'].'
                </div>

            </div>

        </div>
    </section>
    <!-- /Info -->';
    return $display;
}
function initSignin($codeLang){
    $display="<section id='pricing' class='pricing-wrapper pt-100 pb-70'>
            <div class='container'>
            <form id='form_account' name='form_account' action='login.php' method='post'>
                <div class='row'>
                    <div class='col-md-4 ml-auto mr-auto'>
                        <div class='price-box scrollReveal sr-scaleDown sr-ease-in-out-back'>
                            <h3 id='titleForm'>Login</h3>
                            <div id='contentForm'>
                                <div class='form-group'>
                                    <input type='email' name='username' id='username' class='form-control' placeholder='Email address' required >
                                </div>
                                <div class='form-group'>
                                    <input type='password' name='password' id='password' class='form-control' placeholder='password' required >
                                </div>
                                <div class='price-footer'>
                                <input type='button' type='submit' class='btn btn-skin-border mr-2 mb-2' value='LoginP' />
                                <input type='button' onclick='jaxon_signin(\"".$codeLang."\",jaxon.getFormValues(\"form_account\"));return false;' class='btn btn-skin-border mr-2 mb-2' value='Login' />
                                
                            </div>
                            </div>
                            
                        </div>
                        <!--price box-->
                    </div>
                    <!--price col-->
                </div>
            </form>
            </div>
            <!--container-->
        </section> ";
    //jaxon.getFormValues(\"form_account\")
    Return $display;
}
function displayWelcome(){
    Global $lang;
    $title = $lang['POPUP_WELCOME_TITLE'];
    $display=' <div class="row">
                    <div class="col-lg-8 mr-auto ml-auto">
                        <div class="ico-light ico-lg ico-rounded ico-hover sr-delay-1">
                            <div class="text-center">
                                <div class="col-4 ml-auto mr-auto">
                                    <img src="'.$lang['POPUP_WELCOME_ICON'].'" class="img-fluid text-center" alt="service-img">
                                </div>
                                <h5 text-center>'.$lang['POPUP_WELCOME_SUBTITLE'].'</h5>
                            </div>
                            <h6>'.$lang['POPUP_WELCOME_CONTENT'].'</h6>                            
                        </div>
                    </div>
                </div>';
    
    $submitButton = "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>";  
    
    
    $response = new Response();   
    $response->assign('modal-content', 'innerHTML', $display);
    $response->assign('modal-footer', 'innerHTML', $submitButton);
    $response->script('$("#myModalLabel").html("'.$title.'");$("#myModal").modal({"show":true});');
    return $response;
    
}
function displayWelcomeEvent($user_id,$Event = FALSE){
    Global $lang;
    
    switch ($Event) {

        case "SSU_PUMPKIN_CHALLENGE":
            $EventForm = '<form class="form-inline" id="form_weight" name="form_weight" novalidate="novalidate">                            
                            <div class="form-group mx-sm-3 mb-2">
                              <label for="weight" class="sr-only">Weight</label>
                              <input type="text" class="form-control" id="weight" name="weight" placeholder="カボチャの重さをグラムでご記入ください。">
                            </div>
                            <input type="button" onclick="jaxon_saveUserChallenge('.$user_id.',jaxon.getFormValues(\'form_weight\'));return false;" class="btn btn-skin-border mr-2 mb-2" value="送信" />
                          </form>';
            $lang['POPUP_WELCOME_ICON'] = "images/ss_pumpkin.jpg";
            $lang['POPUP_WELCOME_SUBTITLE'] = "PUMPKIN CHALLENGE";
            $lang['POPUP_WELCOME_CONTENT'] = "季節は秋。今年もハロウィーンの時期になりました。<br>Yourコンシェルジュでは、大人も楽しめるハロウィーンイベントを企画しました！<br><br>その名も“パンプキン・チャレンジ！”<br>ルールは簡単、コンシェルジュデスクに置いてある、大きなかぼちゃの重さを予想して、一番近い重さを当てた方が勝者！<br>その方には賞品もご用意しています。賞品はサプライズ！<br>ぜひ、８階コンシェルジュデスクにてお待ちしております！<br><br>●イベント実施期間：10月23日〜31日<br>●エントリーはYourコンシェルジュのサイトからお申込みください。<br>  ＊会員未登録の方は、オンライン登録をお願いします。";
        
            break;
    }
   
    
    $title = $lang['POPUP_WELCOME_TITLE'];
    $display=' <div class="row">
                    <div class="col-lg-8 mr-auto ml-auto">
                        <div class="feature-icon scrollReveal sr-bottom sr-ease-in-out-quad sr-delay-1">
                            <div class="text-center">
                                <img src="'.$lang['POPUP_WELCOME_ICON'].'" class="img-fluid text-center" alt="service-img">
                                <h5 text-center>'.$lang['POPUP_WELCOME_SUBTITLE'].'</h5>
                            </div>
                            <h6>'.$lang['POPUP_WELCOME_CONTENT'].'</h6>
                            <p>'.$EventForm.'</p>
                        </div>
                    </div>
                </div>';
    
    $response = new Response();    
    $response->assign('contentModal', 'innerHTML', $display);    
    $response->script('$("#modal-title").html("'.$title.'");$("#modalp").modal({"show":true});');
    //$(document).ready(function() {$('#myModal').on('shown.bs.modal', function() {$('#myInput').trigger('focus');});});
    return $response;
}
function displaySectionHomeLogged($idLang='jp',$name,$email){
    Global $lang;
    if($GLOBALS['company']['sections']['header_alert']){
        $sectionStart = '<section class="theme-color mt-5">
                        <div class="display-table">
                           <div class="display-table-cell vertical-align-middle">
                               <div class="container text-center">
                               <div class="alert alert-primary">'.$lang['SECTION_HEADER_ALERT'].'</div>';
    }else{
        $sectionStart = '<section class="theme-color  mt-50">
                        <div class="display-table">
                           <div class="display-table-cell vertical-align-middle">
                               <div class="container text-center">';
        /*
        $sectionStart = '<section class="mt-50">
                        <div class="display-table">
                           <div class="display-table-cell vertical-align-middle">
                               <div class="container">';
         * 
         */
        
    }
    
    $sectionEnd = '</div>
                </div>
            </div>
     </section>';
    
    if ($lang['BOT_URL']){
        $name_encoded= urlencode($name);
        $bot_url = $lang['BOT_URL']."?name=".$name."&mail=".$email;

        $display = '<div class="row">
                        <div class="text-center col-lg-8 mr-auto ml-auto mt-20">
                            <div style="width: 100%; height: 500px; position: relative; overflow: hidden;">
                                <iframe width="100%" height="100%" frameborder="0" src="'.$bot_url.'"></iframe>
                            </div>      
                        </div>
                    </div>';
        $section = $sectionStart.$display.$sectionEnd;
    }
    elseif(!empty($lang['SECTION_HOME_LOGGED'])){
        $display = '<div class="text-center col-lg-10 mr-auto ml-auto mb-20 mt-10">
                    <h5 class="lead fw-300 wow fadeInUp" data-wow-delay="0.7s">'.$lang['SECTION_HOME_LOGGED'].'</h5>
                </div>';
        
        $section = $sectionStart.$display.$sectionEnd;
    }
    else{
        $section = '<section class="section-xs"></section>';
    }
    
    return $section;
}
function displaySectionHomeUnLogged($idLang='jp'){
    Global $lang;   Global $company;

    switch ($company['id']) {
        case '35001064691':
            $section = '<section id="slider" class="fullheight">
            <div class="swiper-container" data-effect="slide" data-autoplay="5000">
                <div class="swiper-wrapper">

                    <!-- SLIDE 1 -->
                    <div class="swiper-slide" style="background-image: url(\'images/welcome/welcome1.jpg\');">
                        <div class="overlay dark-1"><!-- dark overlay [1 to 9 opacity] --></div>                        
                        <div class="display-table">
                            <div class="display-table-cell vertical-align-middle">
                                <div class="container">

                                    <div class="row">
                                        <div class="text-center col-md-8 col-12 offset-md-2">

                                            <h1 class="bold wow fadeInUp" data-wow-delay="0.4s"><span class="text-white">'.$lang['SECTION_HOME_TITLE'].'</span></h1>
                                            <p class="lead fw-300 hidden-xs-down wow fadeInUp" data-wow-delay="0.6s"><span class="text-white">'.$lang['SECTION_HOME_BODY'].'</span></p>                                            
                                            <a href="signup.php" class="btn btn-lg btn-primary wow fadeIn" data-wow-delay="1s">'.$lang['SIGNUP_TEXT'].'</a>                        
                                            <a href="login.php" class="btn btn-lg btn-primary wow fadeIn" data-wow-delay="1s">'.$lang['SIGNIN_TEXT'].'</a>

                                        </div>
                                    </div>
                        
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <!-- /SLIDE 1 -->

                    <!-- SLIDE 2 -->
                    <div class="swiper-slide" style="background-image: url(\'images/welcome/welcome2.jpg\');">
                        <div class="overlay dark-2"><!-- dark overlay [1 to 9 opacity] --></div>                        
                        <div class="display-table">
                            <div class="display-table-cell vertical-align-middle">
                                <div class="container">

                                    <div class="row">
                                        <div class="text-center col-md-8 col-12 offset-md-2">

                                            <h1 class="bold font-raleway wow fadeInUp" data-wow-delay="0.4s"><span class="text-white">'.$lang['SECTION_HOME_TITLE2'].'</span></h1>
                                            <p class="lead font-lato fw-300 hidden-xs-down wow fadeInUp" data-wow-delay="0.6s"><span class="text-white">'.$lang['SECTION_HOME_BODY2'].'</span></p>                                            
                                            <a href="signup.php" class="btn btn-lg btn-primary wow fadeIn" data-wow-delay="1s">'.$lang['SIGNUP_TEXT'].'</a>                        
                                            <a href="login.php" class="btn btn-lg btn-primary wow fadeIn" data-wow-delay="1s">'.$lang['SIGNIN_TEXT'].'</a>

                                        </div>
                                    </div>
                        
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <!-- /SLIDE 2 -->

                    <!-- SLIDE 3 -->
                    <div class="swiper-slide" style="background-image: url(\'images/welcome/welcome3.jpg\');">
                        <div class="overlay dark-0"><!-- dark overlay [1 to 9 opacity] --></div>                        
                        <div class="display-table">
                            <div class="display-table-cell vertical-align-middle">
                                <div class="container">

                                    <div class="row">
                                        <div class="text-center col-md-8 col-12 offset-md-2">

                                            <h1 class="bold font-raleway wow fadeInUp" data-wow-delay="0.4s"><span class="text-white">'.$lang['SECTION_HOME_TITLE3'].'</span></h1>
                                            <p class="lead font-lato fw-300 hidden-xs-down wow fadeInUp" data-wow-delay="0.6s"><span class="text-white">'.$lang['SECTION_HOME_BODY3'].'</span></p>                                            
                                            <a href="signup.php" class="btn btn-lg btn-primary wow fadeIn" data-wow-delay="1s">'.$lang['SIGNUP_TEXT'].'</a>                        
                                            <a href="login.php" class="btn btn-lg btn-primary wow fadeIn" data-wow-delay="1s">'.$lang['SIGNIN_TEXT'].'</a>

                                        </div>
                                    </div>
                        
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <!-- /SLIDE 3 -->                    
                </div>
                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>

                <!-- Swiper Arrows -->
                <div class="swiper-button-next"><i class="fa fa-angle-right"></i></div>
                <div class="swiper-button-prev"><i class="fa fa-angle-left"></i></div>                
            </div>
					
			</section>
			<!-- /SLIDER -->';
        break;
        case '0000000':
            //Wellness
            $section = '<section id="slider" class="fullheight">
            <div class="swiper-container" data-effect="slide" data-autoplay="5000">
                <div class="swiper-wrapper">

                    <!-- SLIDE 1 -->
                    <div class="swiper-slide" style="background-image: url(\'images/welcome/welcomewellness.jpg\');">
                        <div class="overlay dark-1"><!-- dark overlay [1 to 9 opacity] --></div>                        
                        <div class="display-table">
                            <div class="display-table-cell vertical-align-middle">
                                <div class="container">
                                    
                        
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <!-- /SLIDE 1 -->                              
                </div>
                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>

                <!-- Swiper Arrows -->
                <div class="swiper-button-next"><i class="fa fa-angle-right"></i></div>
                <div class="swiper-button-prev"><i class="fa fa-angle-left"></i></div>                
            </div>
					
			</section>
			<!-- /SLIDER -->';
        break;
        default:
            $sectionStart = '<section class="theme-color  mt-50">
                                <div class="display-table">
                                    <div class="display-table-cell vertical-align-middle">
                                        <div class="container text-center">';
            $sectionEnd = '</div>
                        </div>
                    </div>
                </section>';

            $display = '<div class="text-center col-lg-8 mr-auto ml-auto mb-20 mt-10">
                            <h5 class="lead fw-300 wow fadeInUp" data-wow-delay="0.7s">'.$lang['SECTION_HOME_TITLE'].'</h5>
                        </div>
                        <div class="mt-30">
                                <a href="signup.php" class="btn btn-lg btn-primary wow fadeInUp" data-wow-delay="1s">'.$lang['SIGNUP_TEXT'].'</a>                        
                                <a href="login.php" class="btn btn-lg btn-primary wow fadeInUp" data-wow-delay="1s">'.$lang['SIGNIN_TEXT'].'</a>
                        </div>';
            $section = $sectionStart.$display.$sectionEnd;
        break;
    }
    return $section;
}
function displaySectionHomeSoon(){
    Global $lang;
    $sectionStart = '<section class="theme-color  mt-50">
                        <div class="display-table">
                           <div class="display-table-cell vertical-align-middle">
                               <div class="container text-center">';
    $sectionEnd = '</div>
                </div>
            </div>
     </section>';
    
    $display = '<div class="text-center col-lg-8 mr-auto ml-auto mb-20 mt-10">
                    <h5 class="lead fw-300 wow fadeInUp" data-wow-delay="0.7s">'.$lang['COMING_SOON'].'</h5>
                </div> ';
    
    $section = $sectionStart.$display.$sectionEnd;
    return $section;
}
function displaySectionHomeRequestForm($uid){
    Global $lang;
    $title=$lang['REQUEST_FORM_TITLE'];
    //return displaySectionHomeRequestFormEnhanced($uid);
    
    if($GLOBALS['company']['sections']['header_alert']){
        $sectionStart = '<section class="theme-color mt-5">
                        <div class="display-table">
                           <div class="display-table-cell vertical-align-middle">
                               <div class="container text-center">
                                    <div class="alert alert-primary">'.$lang['SECTION_HEADER_ALERT'].'</div>';
    }else{
        $sectionStart = '<section class="theme-color  mt-50">
                        <div class="display-table">
                           <div class="display-table-cell vertical-align-middle">
                               <div class="container text-center">';
    }
    $sectionEnd = '</div>
                </div>
            </div>
     </section>';
    
    
     if ($lang['BOT_URL']){
        $display='<div class="row" >
                    <div class="text-center col-lg-8 mr-auto ml-auto mb-20 mt-10">
                            <h5>'.$lang['REQUEST_FORM_TITLE'].'</h5>
                            <h6 class="subtitle">'.$lang['REQUEST_FORM_DESC'].'</h6>
                            <br>
                            <form method="post" action="createticket.php" id="form_ticket" name="form_ticket" novalidate="novalidate">
                                <div class="clearfix">
                                    <div class="form-group">
                                            <input type="text" name="sendersubject" id="sendersubject" class="form-control" placeholder="'.$lang['REQUEST_ENTER_SUBJECT'].'">                                        
                                    </div>
                                    <div class="form-group">
                                            <textarea rows="5" class="form-control" id="sendermessage" name="sendermessage" placeholder="'.$lang['REQUEST_ENTER_MSG'].'"></textarea>                                    
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="button" onclick="jaxon_newTicket(\''.$uid.'\',jaxon.getFormValues(\'form_ticket\'));return false;" class="btn btn-primary btn-lg wow fadeInUp" value="'.$lang['REQUEST_SEND_BUTTON'].'" />
                                    <input type="button" onclick="jaxon_cancelFormTicket();return false;" class="btn btn-primary btn-lg wow fadeInUp" value="'.$lang['REQUEST_CANCEL_BUTTON'].'" />
                                </div>
                            </form>                                   
                    </div>
            </div>';
     }
     else {
          $display = '<div class="text-center col-lg-10 mr-auto ml-auto mb-20 mt-10">
                    <h5 class="lead fw-300 wow fadeInUp" data-wow-delay="0.7s">'.$lang['SECTION_HOME_LOGGED'].'</h5>
                </div>';
     }
     
    $section = $sectionStart.$display.$sectionEnd;
    return $section;
}
function displaySectionHomeRequestFormEnhanced($uid){
    Global $lang;
    $title=$lang['REQUEST_FORM_TITLE'];
    
    $sectionStart = '<section class="mt-50">
                        <div class="display-table">
                           <div class="display-table-cell vertical-align-middle">
                               <div class="container">';
    $sectionEnd = '</div>
                </div>
            </div>
     </section>';
    
        $display='
                    <div class="mr-auto ml-auto mb-10 mt-10">
                        <div class="box-static box-border-top p-30">
                            <div class="box-title mb-30">
                                    <h2 class="fs-20">'.$lang['REQUEST_FORM_TITLE'].'</h2>
                            </div>
                            <h5>'.$lang['REQUEST_FORM_DESC'].'</h5>
                            <br>
                            <form id="form_ticket" name="form_ticket" onsubmit="return jaxon_newTicket2(\''.$uid.'\',jaxon.getFormValues(\'form_ticket\'))">
                                <div class="clearfix"> 
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                              <label for="participant">'.$lang["REQUEST_PARTICIPANT"].' *</label>
                                              <select class="form-control" id="participant" name="participant" required>
                                                  <option value="">---</option>
                                                  <option value="三宅本部長">三宅本部長</option>
                                                  <option value="三谷事業部長">三谷事業部長</option>
                                                  <option value="その他(Other)">その他(Other)</option>                                              
                                                </select>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                          <label for="input_guest">ゲスト出席者<br>会社名またはお名前で検索し、選択後に「確定」を押してください。<br>未登録ゲストは「新規登録」から登録し、「確定」を押してください。</label>
                                          <input type="text" class="form-control form-control-sm" id="input_guest" name="input_guest">
                                        </div>
                                         <div class="form-group col-md-6 align-self-md-end">
                                                <input type="button" onclick="jaxon_addGuestToList(jaxon.getFormValues(\'form_ticket\'));return false;" class="btn btn-primary" value="確定" />
                                                <input type="button" onclick="jaxon_createGuestForm();return false;" class="btn btn-primary" value="新規登録" />
                                                
                                        </div>                                       
                                    </div>
                                    <div id="contentGuestList"></div>
                                    <div class="invisible">
                                        <input type="text" name="customer_id" id="customer_id" class="form-control">
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="radio">
                                                    <input type="radio" name="request_type" onclick="jaxon_displayRequestFormType(1,'.$uid.')" value="レストラン">
                                                    <i></i> レストラン
                                            </label>

                                            <label class="radio">
                                                    <input type="radio" name="request_type" onclick="jaxon_displayRequestFormType(2,'.$uid.')" value="ゴルフ">
                                                    <i></i> ゴルフ
                                            </label>
                                            <label class="radio">
                                                    <input type="radio" name="request_type" onclick="jaxon_displayRequestFormType(3,'.$uid.')" value="ギフト">
                                                    <i></i> ギフト
                                            </label>
                                        </div>
                                    </div>
                                    <div id="contentByType"></div>
                            </div>
                            </form>
                    </div>
            </div>';
     
    
    $section = $sectionStart.$display.$sectionEnd;
    return $section;
}
function displayRequestFormType($type,$uid){
    Global $lang;

    Global $scriptJQueryLoad;
    switch ($type) {
        case 1: //restaurant
            $customForm ='
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="input_date">'.$lang["REQUEST_RESTAURANT_DATE"].' *</label>
                              <input type="text" class="form-control form-control-sm datepicker" data-format="yyyy-mm-dd" data-lang="en" id="input_date" name="input_date" required>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="input_time">'.$lang["REQUEST_TIME"].' *</label>
                              <input type="text" class="form-control form-control-sm" id="input_time" name="input_time" required>
                            </div>
                        </div>  
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="input_nbpeople">'.$lang["REQUEST_NB_PEOPLE"].' *</label>
                              <input type="text" class="form-control form-control-sm" id="input_nbpeople" name="input_nbpeople" required>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="input_budjet">'.$lang["REQUEST_RESTAURANT_BUDGET"].' *</label>
                              <input type="text" class="form-control form-control-sm" id="input_budjet" name="input_budjet" required>
                            </div>
                        </div>                    
                    
                   <div class="form-row">
                         <div class="form-group col-md-8">
                            <label for="input_area">'.$lang["REQUEST_PLACE"].' *</label>
                           <input type="text" name="input_area" id="input_area" class="form-control" required>                           
                        </div>
                        <div class="form-group col-md-4">
                             <label for="private_room">'.$lang["REQUEST_PRIVATE_ROOM"].' *</label>
                             <select class="form-control" id="private_room" name="private_room" required>
                                 <option value="">---</option>
                                 <option value="個室希望">個室希望</option>
                                 <option value="半個室も可">半個室も可</option>
                                 <option value="こだわらない">こだわらない</option>
                               </select>
                        </div>                       
                   </div>
                   <div class="form-group">
                           <label for="input_desc">'.$lang["REQUEST_RESTAURANT_DESC"].'</label>
                           <textarea rows="5" class="form-control" id="input_desc" name="input_desc" ></textarea>                                    
                   </div>
                   <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="restaurant_reservation">'.$lang["REQUEST_RESERVATION"].' *</label>
                            <select class="form-control" id="restaurant_reservation" name="restaurant_reservation" required>
                                <option value="">---</option>
                                <option value="予約希望">予約希望</option>
                                <option value="予約不要">予約不要</option>                                            
                              </select>
                        </div>
                    </div>'; 
            break;
        
        case 2: //golf
            $customForm ='
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="input_date">'.$lang["REQUEST_GOLF_DATE"].' *</label>
                              <input type="text" class="form-control form-control-sm datepicker" data-format="yyyy-mm-dd" data-lang="en" id="input_date" name="input_date" required>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="input_time">'.$lang["REQUEST_TIME"].' *</label>
                              <input type="text" class="form-control form-control-sm" id="input_time" name="input_time" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                              <label for="input_nbpeople">'.$lang["REQUEST_NB_PEOPLE"].' *</label>
                              <input type="text" class="form-control form-control-sm" id="input_nbpeople" name="input_nbpeople" required>
                            </div>
                            <div class="form-group col-md-6">
                              <label for="input_budjet">'.$lang["REQUEST_GOLF_BUDGET"].' *</label>
                              <input type="text" class="form-control form-control-sm" id="input_budjet" name="input_budjet" required>
                            </div>
                        </div>
                    <br /><br />
                    <div class="form-group">
                            <label for="input_area">'.$lang["REQUEST_PLACE"].' *</label>
                           <input type="text" id="input_area" name="input_area" class="form-control" required>                                        
                   </div>
                    
                   <div class="form-group">
                           <label for="input_desc">'.$lang["REQUEST_OTHERS"].'</label>
                           <textarea rows="5" class="form-control" id="input_desc" name="input_desc" ></textarea>                                    
                   </div>';
            break;
        
        case 3: //Gift
            $customForm ='                        
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="input_date">'.$lang["REQUEST_GIFT_DATE"].'*</label>
                           <input type="text" id="input_date" name="input_date" class="form-control" required>                                        
                        </div>
                        <div class="form-group col-md-6">
                                 <label for="input_budjet">'.$lang["REQUEST_GIFT_BUDGET"].' *</label>
                                <input type="text" id="input_budjet" name="input_budjet" class="form-control" required>                                        
                        </div>
                    </div> 
                   <div class="form-group">
                           <label for="input_desc">'.$lang["REQUEST_GIFT_DESC"].'</label>
                           <textarea rows="5" class="form-control" id="input_desc" name="input_desc" ></textarea>                                    
                   </div>';
            break;
    default:
        break;
    }
    $display = $customForm;
     $display .= '
               <div class="form-group">             
                   <input type="button" onclick="jaxon_cancelFormTicketEnhanced();return false;" class="btn btn-primary btn-lg wow fadeInUp" value="'.$lang['REQUEST_CANCEL_BUTTON'].'" />
                   <input type="submit" class="btn btn-primary btn-lg wow fadeInUp" value="'.$lang['REQUEST_SEND_BUTTON'].'" />
               </div>';
     
     
    $response = new Response();   
    $response->assign('contentByType', 'innerHTML', $display);
    return $response;
}
function addGuestToList($ticketForm){
    
    $display ='KO';
    if (isset($ticketForm['customer_id']) && isset($ticketForm['input_guest'])){
        $customer_id = $ticketForm['customer_id'];
        $customer_value = $ticketForm['input_guest'];
        
        $_SESSION['Guests'][$customer_id] = $customer_value;
        
        $display = '<ul class="list-group col-md-6">';
        
        foreach ($_SESSION['Guests'] as $key => $value) {
            $display .= '<li onClick="jaxon_removeCustomerFromGuests('.$key.');return false;" class="list-group-item"><i class="fa fa-times"></i>'.$value.'</li>';
        }

        $display .= '</ul>';
    } 
    $response = new Response();   
    $response->assign('contentGuestList', 'innerHTML', $display);
    $response->assign('input_guest', 'value', '');
    //$response->alert(print_r($_SESSION['Guests'],TRUE));
    return $response;
    
}
function createGuestForm(){
    Global $lang;
   
    //$title = $lang['SURVEY_QUESTION_DEFAULT'];
    $title = 'ゲスト出席者';
    $bodyForm = '<form id="form_addGuest" name="form_addGuest" onsubmit="return jaxon_createCustomer(jaxon.getFormValues(\'form_addGuest\'))">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                           <label for="customer_company_name">会社名 *</label>
                           <input type="text" class="form-control form-control-sm" id="customer_company_name" name="customer_company_name" required>
                         </div>
                       <div class="form-group col-md-6">
                         <label for="customer_department">部署名</label>
                         <input type="text" class="form-control form-control-sm" id="customer_department" name="customer_department">
                       </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                           <label for="customer_title">役職名</label>
                           <input type="text" class="form-control form-control-sm" id="customer_title" name="customer_title">
                         </div>
                       <div class="form-group col-md-6">
                         <label for="customer_name">お客様名 *</label>
                         <input type="text" class="form-control form-control-sm" id="customer_name" name="customer_name" required>
                       </div>
                    </div>'; 
    
    
    $bodyForm.='<div class="form-footer text-center">
                    <button type="button" class="btn btn-default closeModal" data-dismiss="modal">'.$lang['BUTTON_CANCEL'].'</button>
                    <button type="submit" class="btn btn-primary">新規登録</button>
                </div>
                </form>';
    
    $response = new Response();
    $response->assign('contentModal', 'innerHTML', $bodyForm);
    $response->script('$("#modal-title").html("'.$title.'");$("#modalp").modal({"show":true});');
    //$response->script($scriptRating);
    return $response;
}