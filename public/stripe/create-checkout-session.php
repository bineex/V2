<?php
session_start();
require '../../../../vendor/autoload.php';

require 'config.php';
$date_end = strtotime('2020-04-30');
echo $date_end;
echo ;
\Stripe\Stripe::setApiKey($config['stripe_secret_key']);
$domain_url = $config['domain'];
// Create new Checkout Session for the order
// Other optional params include:
// [billing_address_collection] - to display billing address details on the page
// [customer] - if you have an existing Stripe Customer ID
// [payment_intent_data] - lets capture the payment later
// [customer_email] - lets you prefill the email input in the form
// For full details see https://stripe.com/docs/api/checkout/sessions/create

// ?session_id={CHECKOUT_SESSION_ID} means the redirect will have the session ID set as a query param
$checkout_session = \Stripe\Checkout\Session::create([
	'success_url' => $domain_url . '/signup.php?session_id={CHECKOUT_SESSION_ID}',
	'cancel_url' => $domain_url . '/signup.php?cancel_id='.$_SESSION['subscription']['fd_id'].'-'.$_SESSION['subscription']['user_id'],
	'payment_method_types' => ['card'],
        'customer_email' => $_SESSION['subscription']['email'],
        'client_reference_id' => $_SESSION['subscription']['fd_id'],
        'mode' => 'subscription',
	'subscription_data' => [
            'items' => [
                ['plan' => $_SESSION['subscription']['plan'],]
            ],
            'default_tax_rates' => [$_SESSION['subscription']['tax']],
            'billing_cycle_anchor' => $date_end,
        ]
]);
unset($_SESSION['subscription']);
echo json_encode(['sessionId' => $checkout_session['id']]);
