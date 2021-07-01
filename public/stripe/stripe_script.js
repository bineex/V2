var createCheckoutSession = function() {
  return fetch("./stripe/create-checkout-session.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
  }).then(function(result) {
    return result.json();
  });
};

// Handle any errors returned from Checkout
var handleResult = function(result) {
  if (result.error) {
    var displayError = document.getElementById("error-message");
    displayError.textContent = result.error.message;
  }
};

/* Get your Stripe publishable key to initialize Stripe.js */
fetch("./stripe/get_config.php")
  .then(function(result) {
    return result.json();
  })
  .then(function(json) {
    var publicKey = json.publicKey;

    var stripe = Stripe(publicKey);
    // Setup event handler to create a Checkout Session when button is clicked

      var disp = createCheckoutSession().then(function(data) {
          // Call Stripe.js method to redirect to the new Checkout page
          stripe
            .redirectToCheckout({
              sessionId: data.sessionId
            })
            .then(handleResult);
        });

  });
