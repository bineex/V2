<?php
date_default_timezone_set('UTC');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';
require_once '../init.php';
require_once '../includes/header.php';

$confStripe = $company['stripe'];
\Stripe\Stripe::setApiKey($confStripe['stripe_secret_key']);

if(isset($_GET['sub_id']) && !empty($_GET['sub_id'])){
  $session_id = $_GET['sub_id'];
  $checkout_session = \Stripe\Checkout\Session::retrieve($session_id);
  $subscription = \Stripe\Subscription::retrieve($checkout_session->subscription);
  print_r($subscription);
  exit;
}

unset($_SESSION['subscription']);
$_SESSION['subscription']['email'] = 'mario@bineex.com';
$_SESSION['subscription']['fd_id'] = '123456789';
$_SESSION['subscription']['user_id'] = '123';
$_SESSION['subscription']['plan'] = $company['subscription_id'];
$_SESSION['subscription']['url'] = "https://".$_SERVER['SERVER_NAME'];
$_SESSION['subscription']['success_url'] = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."&sub_id={CHECKOUT_SESSION_ID}";
$_SESSION['subscription']['cancel_url'] = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$_SESSION['subscription']['config'] = $company['stripe'];


echo json_encode(['publicKey' => $confStripe['stripe_publishable_key']])."<br><br>";


try {    
  $checkout_session = \Stripe\Checkout\Session::create([
    'success_url' => $_SESSION['subscription']['success_url'],
    'cancel_url' => $_SESSION['subscription']['cancel_url'],
    'payment_method_types' => ['card'],
          'customer_email' => $_SESSION['subscription']['email'],
          'client_reference_id' => $_SESSION['subscription']['fd_id'],
          'mode' => 'subscription',
    'subscription_data' => [
              'items' => [
                  ['plan' => $_SESSION['subscription']['plan'],]
              ],
              'default_tax_rates' => [$confStripe['tax']],
              'trial_from_plan' => true,
          ]
  ]);
    //print_r($checkout_session);
    print_r($checkout_session->id);
  } catch(\Stripe\Exception\CardException $e) {
    // Since it's a decline, \Stripe\Exception\CardException will be caught
      echo 'Status is:' . $e->getHttpStatus() . '\n';
      echo 'Type is:' . $e->getError()->type . '\n';
      echo 'Code is:' . $e->getError()->code . '\n';
    // param is '' in this case
    echo 'Param is:' . $e->getError()->param . '\n';
    echo 'Message is:' . $e->getError()->message . '\n';
  } catch (\Stripe\Exception\RateLimitException $e) {
    echo 'Status is:' . $e->getHttpStatus() . '\n';
    echo 'Type is:' . $e->getError()->type . '\n';
    echo 'Code is:' . $e->getError()->code . '\n';
    // param is '' in this case
    echo 'Param is:' . $e->getError()->param . '\n';
    echo 'Message is:' . $e->getError()->message . '\n';
  } catch (\Stripe\Exception\InvalidRequestException $e) {
      echo 'Status is:' . $e->getHttpStatus() . '\n';
      echo 'Type is:' . $e->getError()->type . '\n';
      echo 'Code is:' . $e->getError()->code . '\n';
      // param is '' in this case
      echo 'Param is:' . $e->getError()->param . '\n';
      echo 'Message is:' . $e->getError()->message . '\n';
  } catch (\Stripe\Exception\AuthenticationException $e) {
      echo 'Status is:' . $e->getHttpStatus() . '\n';
      echo 'Type is:' . $e->getError()->type . '\n';
      echo 'Code is:' . $e->getError()->code . '\n';
      // param is '' in this case
      echo 'Param is:' . $e->getError()->param . '\n';
      echo 'Message is:' . $e->getError()->message . '\n';
  } catch (\Stripe\Exception\ApiConnectionException $e) {
      echo 'Status is:' . $e->getHttpStatus() . '\n';
      echo 'Type is:' . $e->getError()->type . '\n';
      echo 'Code is:' . $e->getError()->code . '\n';
      // param is '' in this case
      echo 'Param is:' . $e->getError()->param . '\n';
      echo 'Message is:' . $e->getError()->message . '\n';
  } catch (\Stripe\Exception\ApiErrorException $e) {
      echo 'Status is:' . $e->getHttpStatus() . '\n';
      echo 'Type is:' . $e->getError()->type . '\n';
      echo 'Code is:' . $e->getError()->code . '\n';
      // param is '' in this case
      echo 'Param is:' . $e->getError()->param . '\n';
      echo 'Message is:' . $e->getError()->message . '\n';
  } catch (Exception $e) {
    echo 'Status is:' . $e->getHttpStatus() . '\n';
      echo 'Type is:' . $e->getError()->type . '\n';
      echo 'Code is:' . $e->getError()->code . '\n';
      // param is '' in this case
      echo 'Param is:' . $e->getError()->param . '\n';
      echo 'Message is:' . $e->getError()->message . '\n';
    print_r($e);
  }

?>

<div id="wrapper">
<div class="spinner-border" role="status">
  <span class="sr-only">Loading...</span>
</div>

</div>
<!-- JAVASCRIPT FILES -->
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
  var stripe = Stripe('<?php echo $confStripe['stripe_publishable_key']; ?>');
  stripe.redirectToCheckout({
    sessionId: '<?php echo $checkout_session->id; ?>'
}).then(function (result) {
  // If `redirectToCheckout` fails due to a browser or network
  // error, display the localized error message to your customer
  // using `result.error.message`.
});
</script>


  </body>
</html>