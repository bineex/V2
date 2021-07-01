<?php
/*
    header('Access-Control-Allow-Origin:*');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
    header('Access-Control-Allow-Methods: GET, POST, PUT');
*/
require '../vendor/autoload.php';
require_once '../init.php';

$db = DB::getInstance();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$confStripe = $company['stripe'];

print_r($confStripe);
// Make sure the configuration file is good.
if (!$confStripe) {
	http_response_code(500);
	echo json_encode([ 'error' => 'Internal server error.' ]);
	exit;
}

\Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
	$event = \Stripe\Webhook::constructEvent($payload, $sig_header, $confStripe['stripe_webhook_secret']);
	
} catch (Exception $e) {
	http_response_code(400);
	echo json_encode([ 'error' => $e->getMessage() ]);
	exit;
}

/*
// Handle the event
switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object; // contains a StripePaymentIntent
        handlePaymentIntentSucceeded($paymentIntent);
        break;
    case 'payment_method.attached':
        $paymentMethod = $event->data->object; // contains a StripePaymentMethod
        handlePaymentMethodAttached($paymentMethod);
        break;
    // ... handle other event types
    default:
        // Unexpected event type
        http_response_code(400);
        exit();
}
*/


file_put_contents('log/eventhook_'.date("y-m-d").'.log', "\xEF\xBB\xBF".date('y-m-d H:i:s').' - '.$event->type.' => '.json_encode($event->data->object,JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND);
echo json_encode($event->data->object, JSON_PRETTY_PRINT);


?>

