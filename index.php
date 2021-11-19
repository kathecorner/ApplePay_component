<?php

// 1. prepare api request to adyen library
// 2. get all payment methods for this shopper
// SGD, SG

$url = "https://checkout-test.adyen.com/v66/paymentMethods";

$payload = array(
  "merchantAccount" => "KenjiW",
  "amount" => [
    "value" => 100,
    "currency" => "EUR",
    ]
    //"shopperReference" => "Shopper_16082021_1"
);

$curl_http_header = array(
   "X-API-Key: AQEyhmfxL4PJahZCw0m/n3Q5qf3VaY9UCJ1+XWZe9W27jmlZiv4PD4jhfNMofnLr2K5i8/0QwV1bDb7kfNy1WIxIIkxgBw==-lUKXT9IQ5GZ6d6RH4nnuOG4Bu//eJZxvoAOknIIddv4=-<anpTLkW{]ZgGy,7",
   "Content-Type: application/json"
);

$curl = curl_init();

curl_setopt_array(
    $curl,
    [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => $curl_http_header,
        CURLOPT_VERBOSE        => true
    ]
);

$paymentmethodsrequestresponse = json_encode(curl_exec($curl));

curl_close($curl);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>how to render objects</title>
    <link rel="stylesheet"
 href="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/4.7.0/adyen.css"
 integrity="sha384-dkJjySvUD62j8VuK62Z0VF1uIsoa+APxWLDHpTjBRwQ95VxNl7oaUvCL+5WXG8lh"
 crossorigin="anonymous">

 <script src="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/4.7.0/adyen.js"
 integrity="sha384-Hmnh/5ShP0Q8iCjGV2U/6XFi7jiiFys4fsh7UrCH1JT1PV1ThZ9czMnbbyjzxuhU"
 crossorigin="anonymous"></script>

 <script src="https://code.jquery.com/jquery-3.6.0.min.js" charset="utf-8"></script>
  </head>
  <body>

    <h1>ApplePay Components</h1>

    <!-- Embed the Adyen Web script element above any other JavaScript in your checkout page. -->

    <!--<div id="dropin-container"></div>-->
      <div id="applepay-container"></div>

    <script type="text/javascript">

    var availablePaymentMethods = JSON.parse( <?php echo $paymentmethodsrequestresponse; ?> );

    function makePayment(state) {
          const prom_data = state;
          return new Promise(
              function (resolve,reject) {
                  $.ajax(
                      {
                          type: "POST",
                          url: "/processpayment.php",
                          data: prom_data,
                          success: function (response) {
                              resolve(response);
                          }
                      }
                  );
              }
          );

      }

const applePayConfiguration = {
    environment: 'test', // Change to 'live' for the live environment.
    clientKey: 'test_RKKBP5GHOFFUFJJMJHOJAG7ZIIJKBMI6', // Public key used for client-side authentication: https://docs.adyen.com/development-resources/client-side-authentication
    session: {
      id: 'CSD9CAC3...', // Unique identifier for the payment session.
      sessionData: 'Ab02b4c...' // The payment session data.
    },
    amount: {
        value: 1000,
        currency: "USD"
    },
    countryCode: "US",
    //onValidateMerchant is required if you're using your own Apple Pay certificate
    onValidateMerchant: (resolve, reject, validationURL) => {
        // Your server uses the validation URL to request a session from the Apple Pay server.
        // Call resolve(MERCHANTSESSION) or reject() to complete merchant validation.
        validateMerchant(validationURL)
            .then(response => {
            // Complete merchant validation with resolve(MERCHANTSESSION) after receiving an opaque merchant session object, MerchantSession
            resolve(response);
            })
            .catch(error => {
            // Complete merchant validation with reject() if any error occurs
            reject();
            });
    },
    onPaymentCompleted: (result, component) => {
        console.info(result, component);
    },
    onError: (error, component) => {
        console.error(error.name, error.message, error.stack, component);
    },
    paymentMethodsResponse :availablePaymentMethods,
    // Any payment method specific configuration. Find the configuration specific to each payment method:  https://docs.adyen.com/payment-methods
    // For example, this is 3D Secure configuration for cards:
    paymentMethodsConfiguration: {
      hasHolderName: true,
      holderNameRequired: true,
      billingAddressRequired: true
    },
    OnSubmit: (state, component) => {
      makePayment(state.data)
                .then(response => {
                    var responseData = response.action;
                    console.log(response);
                    if(response.action) {
                        //back the data to frontend here
                    }
                    else{
                        showFinalResult(response);
                    }
                })
                .catch(error => {
                    console.log(error);
                    throw Error(error);
                });
    }
};

const checkout = new AdyenCheckout(applePayConfiguration);
//const applePayComponent = checkout.create('applepay', applePayConfiguration);

// Create an instance of AdyenCheckout using the configuration object.
//const applePayComponent = new AdyenCheckout(applePayConfiguration);
//const applePayComponent = checkout.create('applepay', applePayConfiguration);
// Create an instance of Drop-in and mount it to the container you created.
//const dropinComponent = checkout.create('dropin').mount('#dropin-container');
const applePayComponent = checkout.create('applepay', applePayConfiguration).mount('#applepay-container');
/*
applePayComponents
   .isAvailable()
   .then(() => {
       applePayComponent.mount("#applepay-container");
   })
   .catch(e => {
       //Apple Pay is not available
   });*/



</script>
<!--  function makePayment(state) {
          const prom_data = state;
          return new Promise(
              function (resolve,reject) {
                  $.ajax(
                      {
                          type: "POST",
                          url: "/processpayment.php",
                          data: prom_data,
                          success: function (response) {
                              resolve(response);
                          }
                      }
                  );
              }
          );

      }

      function showFinalResult(data){
          //console.log(JSON.parse(data.resultCode));
          //var responseData = JSON.parse(data);
          var responseData = data;

          if(responseData.resultCode == "Authorised"){
              alert('PAYMENT SUCCESSFUL!');
              window.location.href = 'http://127.0.0.1:8080/return.php';
          }
      }

      var configuration = {
        paymentMethodsResponse : availablePaymentMethods,
        clientKey: "test_RKKBP5GHOFFUFJJMJHOJAG7ZIIJKBMI6",
        locale: "en-US",
        showPayButton: true,
        environment: "test",
          hasHolderName: true,//added on Aug30
          holderNameRequired: true,//added on Aug30
          enableStoreDetails: true,//added on Aug30
          billingAddressRequired: true,//added on Aug30
        onSubmit: (state,dropin)=>{
            makePayment(state.data)
                .then(response => {
                    var responseData = response.action;
                    console.log(response);
                    if(response.action) {
                        dropin.handleAction(response.action);
                    }
                    else{
                        showFinalResult(response);
                    }
                })
                .catch(error => {
                    console.log(error);
                    throw Error(error);
                });
        },
        onAdditionalDetails: (state,paymentComponent)=>{
        },
        paymentMethodsConfiguration: {
            card:{
                hasHolderName: true,
                holderNameRequired: true
            }
        }
      }

      const checkout = new AdyenCheckout(configuration);

      const dropin = checkout.create('dropin').mount('#customCard-container');

    -->
  </body>
</html>
