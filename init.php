<?php
require_once 'classes/class.autoloader.php';
session_start();

$theme_color = "yellow";
$sections = array('header_alert' => FALSE,
                    'contact' => TRUE,
                    'how_to' => TRUE,
                    'blog' => TRUE,
                    'about' => FALSE,
                    'event' => TRUE,
                    'eventpayment' => FALSE,
                    'eventdirect' => FALSE,
                    'news' => TRUE,
                    'example' => FALSE,
                    'team' => TRUE,
                    'faq' => TRUE,
                    'card' => FALSE,
                    'service' => FALSE,
                    'programs' => FALSE,
                    'privates' => FALSE,
                    'howto_signup' => FALSE,
                    'price' => FALSE,
                    'info' => FALSE,
                    'callout' => FALSE,
                    'account' => TRUE);

$sections['event_items'] = 6;
/* PPE */
$confStripe['subscription_id'] = '';
$confStripe['tax'] = 'txr_1HFyNPL40IwoyLhvy5Q8DzK0'; 
$confStripe['stripe_webhook_secret'] = 'whsec_64KrbfLkBgPZkENaUw4tL40Np9YO52Aw';
$confStripe['stripe_secret_key'] = 'sk_test_51HFY5oL40IwoyLhvrrRG2Mha8EAOcclubE4qHrxQSMrxZJanSTf8Wqrob0xV5LKJzbvcCzgGkSVvd9UzCYsR0DpX00w2JKR8zk';
$confStripe['stripe_publishable_key'] = 'pk_test_51HFY5oL40IwoyLhvaIp7j1UJ0GUMcBtRH9K73rpatmnTAV2s7px2lOTHXNOLR6v3i1MavrTN0B441hsqtuDfVrH80007rMyKPL';

/* Prod */
$confStripe['subscription_id'] = '';
$confStripe['tax'] = 'txr_1HFYFvL40IwoyLhvSWLmySUA';
$confStripe['stripe_webhook_secret'] = 'whsec_64KrbfLkBgPZkENaUw4tL40Np9YO52Aw';
$confStripe['stripe_secret_key'] = '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';
$confStripe['stripe_publishable_key'] = 'pk_live_51HFY5oL40IwoyLhvxtNrmBwbiWIQnb9V5oOwQMq3flOWFzkRuXAq3rnRtfcHh687C2zHAzyFZpwGa1iNHprxIEpE00hcBIfzkH';

$confStripe['stripe_trial_days'] = 10;

switch ($_SERVER['SERVER_NAME']) {
    case "card.yourconcierge.jp":
        $company = array ('id' =>'000000000','tracking_id' =>FALSE, 'domain'=>'', 'lib' =>'Card', 'unlogged_access' =>FALSE,
            'walk_in'=>FALSE, 'email_customize' => FALSE, 'FAQ' => FALSE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'redirect'=>TRUE,
            'request_form'=> TRUE,
            'ip_restriction'=> array('000.000.000.0000'),
            'language' => 'multi',
            'privacy_doc' => 'privacy_information.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'report' => FALSE,
            'sections' => $sections);
        break;
    case "events.yourconcierge.jp":
        $sections['faq'] = FALSE;
        $sections['how_to'] = FALSE;
        $sections['example'] = FALSE;
        $sections['event_items'] = 9;
        
        $company = array ('id' =>'35001046233','tracking_id' => 'UA-132838601-3', 'domain'=>'tenant', 'lib' =>'Evants', 'unlogged_access' =>TRUE,
            'walk_in'=>TRUE, 'email_customize' => TRUE,'FAQ' => TRUE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'request_form'=> FALSE,
            'redirect'=>FALSE,
            'membership_check' => TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2019.05.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => FALSE,
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "family.yourconcierge.jp":
        $sections['price'] = TRUE;
        $sections['how_to'] = FALSE;
        $sections['howto_signup'] = TRUE;
        $sections['price'] = TRUE;
        /*PPE
        $confStripe['tax'] = 'txr_1GkqSGG4hsAX9JOaRJThGeyJ';
        $confStripe['stripe_webhook_secret'] = 'whsec_HLEUWtKCNfkVmWtWOcuPQrxoMA9FaxKc';
        $confStripe['stripe_secret_key'] = 'sk_test_JCFMm2hG1jaMA9NB9x3xwVJV00eZVd93LR';
        $confStripe['stripe_publishable_key'] = 'pk_test_X9O8pOQFk5EnLjpb3co8SFHa00CHCOsjot';
        */
        /*PROD*/
        $confStripe['subscription_id'] = 'price_HKyhMNkgCoxdYB';
        $confStripe['tax'] = 'txr_1GmkX5G4hsAX9JOads76b71m';
        $confStripe['stripe_webhook_secret'] = 'whsec_Cz1ladhzMVCAhMEo7v7qrOWaF9w705wR';
        $confStripe['stripe_secret_key'] = 'sk_live_A3kuYIUDuN1MmsvVJOmcSJsu00udipMvOZ';
        $confStripe['stripe_publishable_key'] = 'pk_live_ng1A9ft2TK4mmoQKIXgKgXU900wpHaHaI8';
        
        $company = array ('id' =>'35001026668',
            'tracking_id' =>FALSE, 
            'domain'=>'*', 
            'lib' =>'Family', 
            'unlogged_access' =>TRUE,
            'walk_in'=>FALSE, 
            'email_customize' => FALSE,
            'sponsorship_request' => TRUE,
            'FAQ' => FALSE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'redirect'=>FALSE,
            'request_form'=> TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'subscription_id' => $confStripe['subscription_id'],
            'signup_subscription'  => TRUE,
            'privacy_doc' => '(YourConcierge)Privacy Policy_2019.05.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '(Family)Teams of Service 2020.05_.pdf',
            'event' => FALSE,
            'report' => TRUE,
            'sections' => $sections,
            'stripe' => $confStripe);

        break;
    
    case "human-first.yourconcierge.jp":
        $company = ['id' =>'35001288223',
            'tracking_id' => FALSE,
            'domain'=>'*',
            'lib' =>'Human First',
            'unlogged_access' =>TRUE,
            'membership_check' => TRUE,
            'walk_in'=>TRUE,
            'email_customize' => FALSE,
            'FAQ' => TRUE,
            'logo'=>'images/hf_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'request_form'=> TRUE,
            'redirect'=>FALSE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2021.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '(YourConcierge)Teams of Service 2020.02.pdf',
            'event' => FALSE,
            'sections' => $sections];
        break;
    case "imbesideyou.yourconcierge.jp":
        $sections['event'] = FALSE;
        $company = array ('id' =>'35001340541','tracking_id' => FALSE, 'domain'=>'@tpo', 'lib' =>'TPO', 'unlogged_access' =>TRUE,
            'walk_in'=>FALSE, 'email_customize' => FALSE, 'FAQ' => TRUE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',    
            'redirect'=>FALSE,
            'request_form'=> FALSE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2021.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '(YourConcierge)Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "muromachi-mot-lounge.yourconcierge.jp":
        $company = ['id' =>'35001026653',
            'tracking_id' => FALSE,
            'domain'=>'select',
            'lib' =>'mot. Member Lounge',
            'unlogged_access' =>TRUE,
            'membership_check' => TRUE,
            'walk_in'=>TRUE,
            'email_customize' => FALSE,
            'FAQ' => TRUE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'request_form'=> TRUE,
            'redirect'=>FALSE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '[YourConcierge]Privacy Policy_2019.05.pdf',
            'privacy_tpo' => '[TPO]Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.04_mot. member lounge.pdf',
            'event' => FALSE,
            'select_tenant_option' => TRUE,
            'sections' => $sections];
        break;
    case "myaccount.yourconcierge.jp":
        foreach($sections as $key=>$value){ $sections[$key] = FALSE;}
        $sections['service'] = TRUE;
        $sections['programs'] = TRUE;
        $sections['privates'] = TRUE;
        $sections['howto_signup'] = TRUE;
        $sections['price'] = TRUE;
        $sections['info'] = TRUE;
        $sections['callout'] = TRUE;
        $sections['eventpayment'] = TRUE;
        $sections['event_items'] = 9;
       
        /*PPE
        $confStripe['subscription_id'] = 'andyou';
        //$confStripe['subscription_id'] = 'plan_H13OERHhFnQddF';
        $confStripe['tax'] = 'txr_1GkQjTF8FRDOm1FXDfgNGcwP';
        $confStripe['stripe_webhook_secret'] = 'whsec_HLEUWtKCNfkVmWtWOcuPQrxoMA9FaxKc';
        $confStripe['stripe_secret_key'] = 'sk_test_9C3GL67VSuLJ8Dk4BI4qow3E00TKDgz6lE';
        $confStripe['stripe_publishable_key'] = 'pk_test_oXCuS6nq1rlXbHHxZDtgswm100lI7l2PhA';
        */
        /* Prod */
        $confStripe['subscription_id'] = 'price_1HzGV1F8FRDOm1FXESUcbSaw';
        $confStripe['tax'] = 'txr_1GSCfYF8FRDOm1FXJ7TSf3k7';
        $confStripe['stripe_webhook_secret'] = 'whsec_64KrbfLkBgPZkENaUw4tL40Np9YO52Aw';
        $confStripe['stripe_secret_key'] = 'sk_live_891tkIgyc5hiu2YJz74UgoZo00YkGnPGWH';
        $confStripe['stripe_publishable_key'] = 'pk_live_S3qCblnxqhtrRSxLiVjLWvkc00qJocgMDl';

        $confStripe['stripe_trial_days'] = 10;
        
        $company = array ('id' =>'35001064691',
            'tracking_id' =>'UA-132838601-4', 
            'domain'=>'*',
            'lib' =>'myaccount',
            'unlogged_access' =>TRUE,
            'walk_in'=>TRUE,
            'email_customize' => FALSE,
            'FAQ' => FALSE,
            'logo'=>'images/logo/&You-logo.png',
            'redirect'=>FALSE,
            'request_form'=> FALSE,
            'init_bot' =>TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'subscription_id' => $confStripe['subscription_id'],
            'event_subscription' => TRUE,
            'privacy_doc' => '(YourConcierge)Privacy Policy_2021.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[& You]Teams of Service 2020.05_.pdf',
            'event' => FALSE,
            'report' => TRUE,
            'sections' => $sections,
            'stripe' => $confStripe);
        break;
    case "next.yourconcierge.jp":
        $company = array ('id' =>'35058707451','tracking_id' => FALSE, 'domain'=>'@tpo', 'lib' =>'Sky Premium', 'unlogged_access' =>TRUE,
            'walk_in'=>TRUE, 'email_customize' => FALSE, 'FAQ' => TRUE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',    
            'redirect'=>FALSE,
            'request_form'=> FALSE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2021.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "nttdata.yourconcierge.jp":
        $sections['event'] = FALSE;
        $sections['faq'] = TRUE;
        $sections['example'] = FALSE;
        $company = array ('id' =>'35000564654','tracking_id' => FALSE, 'domain'=>'@nttdata.co.jp', 'lib' =>'NTT Data', 'unlogged_access' =>TRUE,
        'walk_in'=>FALSE, 'email_customize' => FALSE, 'FAQ' => TRUE,
        'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/NTT_index.jpg',
        'redirect'=>FALSE,
        'ip_restriction'=> FALSE,
        'request_form'=> FALSE,'SignUp_lite'=> TRUE,
        'language' => 'jp',
        'privacy_doc' => FALSE,
        'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
        't&c_doc' => 'コーポレートコンシェルジュサービス利用条件書_201910.pdf',
        'event' => FALSE,
        'sections' => $sections);
        break;
    case "ocatokyo.yourconcierge.jp":
        $sections['event'] = FALSE;
        $company = ['id' =>'35001238370',
        'tracking_id' => FALSE,
        'domain'=>'*',
        'lib' =>'OCA TOKYO',
        'unlogged_access' =>TRUE,
        'membership_check' => TRUE,
        'walk_in'=>TRUE,
        'email_customize' => FALSE,
        'FAQ' => TRUE,
        'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
        'request_form'=> TRUE,
        'redirect'=>FALSE,
        'ip_restriction'=> FALSE,
        'language' => 'multi',
        'privacy_doc' => '(YourConcierge)Privacy Policy_2019.05.pdf',
        'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
        't&c_doc' => 'OCA_Service Policy_210629.pdf', 
        'event' => FALSE,
        'sections' => $sections];
        break;  
   
    case "ppe.yourconcierge.jp":
        //$sections['header_alert'] = TRUE;
        $company = array ('id' =>'35000511390','tracking_id' =>'UA-132838601-1', 'domain'=>'@dentsu.co.jp', 'lib' =>'Dentsu', 'unlogged_access' =>TRUE,
            'walk_in'=>FALSE,
            'sponsorship_request' => TRUE, 
            'email_customize' => FALSE, 'FAQ' => FALSE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'redirect'=>FALSE,
            'request_form' => TRUE,
            'init_bot' => TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2019.05.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'report' => TRUE,
            'sections' => $sections);
        break;

    case "the-premier-floor-marunouchi.yourconcierge.jp":
        $sections['faq'] = TRUE;
        $theme_color = "green";
        $company = array ('id' =>'35000894238','tracking_id' => FALSE, 'domain'=>'tenant', 'lib' =>'Mitsubishi Estate', 'unlogged_access' =>TRUE,
            'walk_in'=>TRUE, 'email_customize' => FALSE,'FAQ' => TRUE,
            'logo'=>'images/yc_logo_nav_green.png', 'picture'=>'images/yc_logo_welcome.png',
            'request_form'=> TRUE,
            'redirect'=>FALSE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2021.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    
    
    case "tpo.yourconcierge.jp":
        $sections['contact'] = FALSE;
        $company = array ('id' =>'35000695111','tracking_id' => FALSE, 'domain'=>'@', 'lib' =>'TPO', 'unlogged_access' =>TRUE,
            'walk_in'=>FALSE, 'email_customize' => FALSE, 'FAQ' => FALSE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'redirect'=>FALSE,
            'request_form'=> TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => 'privacy_information.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    
    
    case "wellness.yourconcierge.jp":
        foreach($sections as $key=>$value){ $sections[$key] = FALSE;}
        $sections['eventdirect'] = TRUE;
        $sections['contact'] = TRUE;
        
        $company = array ('id' =>'35001261629',
            'tracking_id' =>FALSE, 
            'domain'=>'tenant',
            'lib' =>'myaccount',
            'unlogged_access' =>TRUE,
            'walk_in'=>TRUE,
            'email_customize' => FALSE,
            'FAQ' => FALSE,
            'logo'=>'images/yc_logo_nav.png',
            'redirect'=>FALSE,
            'request_form'=> FALSE,
            'init_bot' => TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'event_subscription' => FALSE,
            'privacy_doc' => '(YourConcierge)Privacy Policy_2021.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '(Wellness)Teams of Service 2020.11_.pdf',
            'event' => FALSE,
            'report' => TRUE,
            'sections' => $sections);
        break;
    
   
   
    
    //closed:
    case "bcg.yourconcierge.jp":
        $sections['faq'] = TRUE;
        $company = array ('id' =>'35001025704','tracking_id' => FALSE, 'domain'=>'@bcg.com', 'lib' =>'BCG', 'unlogged_access' =>TRUE,
            'walk_in'=>FALSE, 'email_customize' => FALSE, 'FAQ' => TRUE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'redirect'=>FALSE,
            'request_form'=> TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_BCG_2020.02.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "cartier.yourconcierge.jp":
        $company = array ('id' =>'35000657203','tracking_id' =>'UA-132838601-2', 'domain'=>'@cartier.com', 'lib' =>'Cartier', 'unlogged_access' =>TRUE,
            'walk_in'=>FALSE, 'email_customize' => FALSE, 'FAQ' => TRUE,
            'logo'=>'images/cartier_logo_nav.png', 'picture'=>'images/cartier_logo_welcome.png',    
            'redirect'=>TRUE,
            'request_form'=> TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2019.05.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "kke.yourconcierge.jp":
        $company = array ('id' =>'35001256115','tracking_id' => FALSE, 'domain'=>'@tpo', 'lib' =>'TPO', 'unlogged_access' =>TRUE,
            'walk_in'=>FALSE, 'email_customize' => FALSE, 'FAQ' => TRUE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',    
            'redirect'=>TRUE,
            'request_form'=> FALSE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2019.05.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "dentsu.yourconcierge.jp":
        //$sections['header_alert'] = TRUE;
        $company = array ('id' =>'35000511390','tracking_id' =>'UA-132838601-1', 'domain'=>'@dentsu.co.jp', 'lib' =>'Dentsu', 'unlogged_access' =>TRUE,
            'walk_in'=>FALSE,
            'sponsorship_request' => TRUE, 
            'email_customize' => FALSE, 'FAQ' => FALSE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'redirect'=>TRUE,
            'request_form'=> FALSE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2019.05.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'report' => TRUE,
            'sections' => $sections);
        break;
    case "paypay.yourconcierge.jp":
        
        $sections['faq'] = TRUE;
        $sections['how_to'] = FALSE;
        $sections['example'] = FALSE;
        $company = array ('id' =>'35001056421','tracking_id' => FALSE, 'domain'=>'paypay-corp.co.jp', 'lib' =>'Paypay', 'unlogged_access' =>TRUE,
            'walk_in'=>TRUE, 'email_customize' => FALSE, 'FAQ' => TRUE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'redirect'=>TRUE,
            'request_form'=> TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2019.05.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "mec.yourconcierge.jp":
        $company = array ('id' =>'35000516945','tracking_id' => FALSE, 'domain'=>'@mec.co.jp', 'lib' =>'Mitsubishi Estate', 'unlogged_access' =>TRUE,
            'walk_in'=>TRUE, 'email_customize' => FALSE,'FAQ' => FALSE,
            'logo'=>'images/mec_logo.png', 'picture'=>'images/mec_image.png',
            'redirect'=>TRUE,
            'ip_restriction'=> array('202.34.151.0/24','211.0.140.192/26','60.236.225.217','119.243.169.114','195.132.161.47','60.157.96.39','221.253.213.147','78.194.225.101','110.4.183.56','180.57.101.138'),
            'language' => 'jp',
            'privacy_doc' => 'privacy_information.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "nttdatatrial.yourconcierge.jp":
        $sections['event'] = FALSE;
        $sections['about'] = TRUE;
        $sections['faq'] = FALSE;
        
        $company = array ('id' =>'35000333164','tracking_id' => FALSE, 'domain'=>'@nttdata.co.jp', 'lib' =>'NTT Data', 'unlogged_access' =>TRUE,
            'walk_in'=>FALSE, 'email_customize' => FALSE, 'FAQ' => FALSE,
            'logo'=>'images/NTT_navbar.jpg', 'picture'=>'images/NTT_index.jpg',
            'redirect'=>FALSE,
            'ip_restriction'=> FALSE,
            'request_form'=> TRUE,
            'language' => 'jp',
            'privacy_doc' => FALSE,
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '【NTTデータ様】コーポレートコンシェルジュサービス_利用条件書.pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "robertwalters.yourconcierge.jp":
        $sections['faq'] = TRUE;
        $company = array ('id' =>'35001009865','tracking_id' => FALSE, 'domain'=>'@robertwalters.co.jp', 'lib' =>'RObert Walters', 'unlogged_access' =>TRUE,
            'walk_in'=>FALSE, 'email_customize' => FALSE, 'FAQ' => TRUE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/yc_logo_welcome.png',
            'redirect'=>FALSE,
            'request_form'=> TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '(YourConcierge)Privacy Policy_2019.05.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
    case "ssu.yourconcierge.jp":
        $company = array ('id' =>'35000516944','tracking_id' => FALSE, 'domain'=>'*', 'lib' =>'Sunny Side Up', 'unlogged_access' =>FALSE,
            'walk_in'=>FALSE, 'email_customize' => FALSE,'FAQ' => FALSE,
            'logo'=>'images/yc_logo_nav.png', 'picture'=>'images/SSUportal.jpeg',
            'redirect'=>TRUE,
            'request_form'=> TRUE,
            'ip_restriction'=> FALSE,
            'language' => 'multi',
            'privacy_doc' => '2019.2.5_TPO_Handling_Personal_Information.pdf',
            'privacy_tpo' => '(TPO)Privacy Policy_2019.04.pdf',
            't&c_doc' => '[YourConcierge]Teams of Service 2020.02 .pdf',
            'event' => FALSE,
            'sections' => $sections);
        break;
     
}
$company['signup_subscription'] = (isset($company['signup_subscription']) ? $company['signup_subscription'] : FALSE);
$company['event_subscription'] = (isset($company['event_subscription']) ? $company['event_subscription'] : FALSE);
if (!isset($company['stripe'])){$company['stripe'] = $confStripe;}
$GLOBALS['company'] = $company;


// Set up Language ressources. default: ja-JP
$lang_eng = array ('id' =>'en', 'code'=>'en', 'lib' =>'ENG');
$lang_jpn = array ('id' =>'jp', 'code'=>'ja-JP', 'lib' =>'JPN');
if ($GLOBALS['company']['language'] == 'multi'){
    if (!isset($_SESSION['lang'])){
        if(isset($_GET['lang']) && $_GET['lang'] == 'en'){
            $_SESSION['lang'] = $lang_eng;
        }else{
            $_SESSION['lang'] = $lang_jpn;
        }
    }
    elseif (isset($_GET['lang']) && $_SESSION['lang'] != $_GET['lang'] && !empty($_GET['lang'])) {
        if ($_GET['lang'] == 'en'){
            $_SESSION['lang'] = $lang_eng;
        }
        elseif ($_GET['lang'] == 'jp') {
            $_SESSION['lang']= $lang_jpn;
        }
    }
}
elseif($GLOBALS['company']['language'] == 'en'){
    $_SESSION['lang'] = $lang_eng;
}
else {
    $_SESSION['lang'] = $lang_jpn;
}

require_once 'languages/'.$_SESSION['lang']['id'].'.php';
// -----------------------------------------------------

$abs_us_root=$_SERVER['DOCUMENT_ROOT'];

$self_path=explode("/", $_SERVER['PHP_SELF']);
$self_path_length=count($self_path);
$file_found=FALSE;

for($i = 1; $i < $self_path_length; $i++){
	array_splice($self_path, $self_path_length-$i, $i);
	$us_url_root=implode("/",$self_path)."/";

	if (file_exists($abs_us_root.$us_url_root.'z_us_root.php')){
		$file_found=TRUE;
		break;
	}else{
		$file_found=FALSE;
	}
}
if ($company['tracking_id']){
    $script_analytics = "<script async src=\"https://www.googletagmanager.com/gtag/js?id=".$company['tracking_id']."\"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', '".$company['tracking_id']."');
    </script>";
} else {
    $script_analytics="";
}

require_once 'helpers/helpers.php';
//require_once 'includes/fct_security';

// Set config
$GLOBALS['config'] = array(
	'mysql'  => array(
                    'host'         => 'localhost',
                    'username'     => 'root',
                    'password'     => 'egM8s2xST5r9',
                    'db'           => 'tpo',
                    ),
        'remember' => array(
                    'cookie_name'   => 'pmqesoxiw318374csb',
                    'cookie_expiry' => 604800  //One week, feel free to make it longer
                  ),
        'session' => array(
                    'session_name' => 'user',
                    'token_name' => 'token',
                  )
);

$GLOBALS['config']['mysql'] = array(
                    'host'         => 'tposerverdb.mysql.database.azure.com',
                    'username'     => 'DBControl@tposerverdb',
                    'password'     => 'egM8s2xST5r9',
                    'db'           => 'tpo',
                    );

//-------------------------------------------------------------------------- 

//If you changed your UserSpice or UserCake database prefix
//put it here.
$db_table_prefix = "uc_";  //Old database prefix

//adding more ids to this array allows people to access everything, whether offline or not. Use caution.
$master_account = [1];

$currentPage = currentPage();
$currentURL = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
//Check to see if user has a remember me cookie
if(Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))){
	$hash = Cookie::get(Config::get('remember/cookie_name'));
	$hashCheck = DB::getInstance()->query("SELECT * FROM users_session WHERE hash = ? AND uagent = ?",array($hash,Session::uagent_no_version()));

	if ($hashCheck->count()) {
		$user = new User($hashCheck->first()->user_id);
		$user->login();

	}
}

//Check to see that user is logged in on a temporary password
$user = new User();

//Check to see that user is verified
if($user->isLoggedIn()){
	if($user->data()->email_verified == 0 && $currentPage != 'verify.php' && $currentPage != 'logout.php' && $currentPage != 'verify_thankyou.php'){
		Redirect::to('users/verify.php');
	}
}

$timezone_string = 'Asia/Tokyo';
date_default_timezone_set($timezone_string);

$scriptJQueryLoad="var scriptTag = document.getElementsByTagName('script');
        var src;

        for (var i = 0; i < scriptTag.length; i++) {
            src = scriptTag[i].src;
            scriptTag[i].parentNode.removeChild(scriptTag[i]);

            try {
                var x = document.createElement('script');
                x.type = 'text/javascript';
                x.src = src;
                //console.log(x)
                document.getElementsByTagName('head')[0].appendChild(x);
            }
            catch (e) {}
        }";
