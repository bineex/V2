<?php
require '../../vendor/autoload.php';
require_once 'config.php';

\Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);

// Fetch the Checkout Session to display the JSON result on the success page
$id = $_GET['sessionId'];
$checkout_session = \Stripe\Checkout\Session::retrieve($id);

echo json_encode($checkout_session);
