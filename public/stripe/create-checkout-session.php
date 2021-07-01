<?php
session_start();
require '../../vendor/autoload.php';
require 'config.php';
//$stripe_trial = (empty($confStripe['stripe_trial_days']) ? "" : "'trial_end' =>".$confStripe['stripe_trial_days'].",");

\Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);

$checkout_session = \Stripe\Checkout\Session::create([
	'success_url' => $_SESSION['subscription']['success_url'],
	'cancel_url' => $_SESSION['subscription']['cancel_url'],
	'payment_method_types' => ['card'],
    'customer_email' => $_SESSION['subscription']['email'],
    'client_reference_id' => $_SESSION['subscription']['fd_id'],
    'mode' => 'subscription',
    'allow_promotion_codes' => true,
    'locale' => 'auto',
	'subscription_data' => [
            'items' => [
                ['plan' => $_SESSION['subscription']['plan'],]
            ],
            'default_tax_rates' => [$confStripe['tax']],
            'trial_from_plan' => true,
        ]
]);
//unset($_SESSION['subscription']);
echo json_encode(['sessionId' => $checkout_session['id']]);
