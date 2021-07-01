<?php
session_start();
require '../../vendor/autoload.php';
require 'config.php';

\Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);

// Create new Checkout Session for the order
if (isset($_SESSION['subscription']['customer']) && !empty($_SESSION['subscription']['customer'])){

  $checkout_session = \Stripe\Checkout\Session::create([
    'success_url' => $_SESSION['subscription']['url'].'&session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $_SESSION['subscription']['url'],
    'payment_method_types' => ['card'],
    'customer' => $_SESSION['subscription']['customer'],    
      'line_items' => [[
          'name' => $_SESSION['subscription']['name'],
          'description' => $_SESSION['subscription']['description'],
          'images' => [$_SESSION['subscription']['images']],
          'amount' => $_SESSION['subscription']['amount'],
          'currency' => 'jpy',
          'quantity' => 1,
        ]],
        'payment_intent_data' => [
          'capture_method' => 'manual',
        ],	
  ]);
}
else{
  
  $checkout_session = \Stripe\Checkout\Session::create([
    'success_url' => $_SESSION['subscription']['url'].'&session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $_SESSION['subscription']['url'],    
    'customer_email' => $_SESSION['subscription']['email'],
    'client_reference_id' => $_SESSION['subscription']['fd_id'],
    'payment_method_types' => ['card'],
      'line_items' => [[
          'name' => $_SESSION['subscription']['name'],
          'description' => $_SESSION['subscription']['description'],
          'images' => [$_SESSION['subscription']['images']],
          'amount' => $_SESSION['subscription']['amount'],
          'currency' => 'jpy',
          'quantity' => 1,
        ]],
        'payment_intent_data' => [
          'capture_method' => 'manual',
        ],	
  ]);
}
//unset($_SESSION['subscription']);
echo json_encode(['sessionId' => $checkout_session['id']]);
