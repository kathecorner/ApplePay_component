<?php

// 1. prepare api request to adyen library
// 2. get all payment methods for this shopper /GetPeyments API
// 3. type: 'upi_collect', currency: INR

// User Registeration 
// PayAsYouGo
// Tokenization/RecurringPayment
// Fraud

$url = "https://checkout-test.adyen.com/v69/paymentMethods";

$payload = array(
  "merchantAccount" => "KenjiW",
  "countryCode" => "US",
  "channel" => "web",
  "amount" => [
    "value" => 1000,
    "currency" => "JPY",
    //"currency" => "JPY",
    ],
    "shopperReference" => "Shopper03" //enable it when need to show tokanization
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

//var_dump($paymentmethodsrequestresponse);

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <link rel="stylesheet"
     href="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/5.27.0/adyen.css"
     integrity="sha384-2MpA/pwUY9GwUN1/eXoQL3SDsNMBV47TIywN1r5tb8JB4Shi7y5dyRZ7AwDsCnP8"
     crossorigin="anonymous">

     <script src="https://checkoutshopper-live.adyen.com/checkoutshopper/sdk/5.27.0/adyen.js"
     integrity="sha384-YGWSKjvKe65KQJXrOTMIv0OwvG+gpahBNej9I3iVl4eMXhdUZDUwnaQdsNV5OCWp"
     crossorigin="anonymous"></script>

     <script src="https://code.jquery.com/jquery-3.6.0.min.js" charset="utf-8"></script>

  <style>
  body {
padding: 10px;
background-color: #fff;
}

p {
font-size: 12px;
line-height: 1.4;
}

table {
margin: 30px 0;
}

table th {
font-size: 12px;
}

#float-box {
overflow: hidden;
}

#float-box img {
float: left;
max-width: 45%;
}

#float-box p {
float: right;
max-width: 50%;
}

img {
max-width: 100%;
height: atuo;
}

#kenjis-dropin {
  max-width: 100%;
  height: atuo;
}

.apple-pay-button {
    display: inline-block;
    -webkit-appearance: -apple-pay-button;
    -apple-pay-button-type: donate; /* Use any supported button type. */
}
.apple-pay-button-black {
    -apple-pay-button-style: black;
}
.apple-pay-button-white {
    -apple-pay-button-style: white;
}
.apple-pay-button-white-with-line {
    -apple-pay-button-style: white-outline;
}

@media screen and (max-width: 400px) {
#float-box img,
#float-box p {
  float: none;
  max-width: 100%;
}
}
  </style>
</head>
	<body>
<form name="myform">
 
      <input name="sample" type="text">
 
</form>
		<button id="btn">press it</button>
</body>
	
	
	
	
  <script type="text/javascript">

	  
	  var form = document.forms.myform;
 
form.sample.addEventListener('change', function() {
  
    alert('textbox detected a change');
  
}, false);

	  btn.addEventListener('click', function() {
  
    alert('button clicked');
  
}, false);

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

    function showFinalResult(data){
        //console.log(JSON.parse(data.resultCode));
        //var responseData = JSON.parse(data);
        var responseData = data;

        if(responseData.resultCode == "Authorised"){
            alert('PAYMENT SUCCESSFUL!');
            //window.location.href = 'http://127.0.0.1:8080/return.php';
            window.location.href = '/showResults.php';
        }
    }

    function show3DSResult(data){
      if(data.resultCode == "Authorised"){

          alert(data.resultCode);

          var response_list = data;
          var response_list_all;

          for (var i=0; i<response_list.length;i++){
            response_list_all += '<li>' + response_list[i] + '</li>';
          }
          //document.getElementById('response_list_all').innerHTML = response_list_all;
          document.write(data.resultCode);
      }
      /*
      console.log("makeAdditionalDetails_2(data)");*/
    }

    function makeAdditionalDetails(state){
      //alert('makeAdditionalDetials');

      const detail_data = state;
      return new Promise(
        function (resolve,reject){
          $.ajax(
            {
              type: "POST",
              url: "additionaldetails.php",
              data: detail_data,
              success: function (response) {
                resolve(response);
                console.log(response);
              }
            }
          );
          }
          )
        }

        const stopProcessing = () => {
          document.location="/showResult.html";
        }
        
    var applePayConfiguration = {
	amount: {        value: 1000,        currency: "EUR"    },
	countrycode: "DE",
	    "requiredBillingContactFields": [
        "postalAddress",
        "name",
        "phoneticName"
    ],
    "requiredShippingContactFields": [
        "postalAddress",
        "name",
        "phone",
        "email"
    ],
    "lineItems": [
        {
            "label": "Sales Tax",
            "amount": "0.00"
        },
        {
            "label": "Shipping",
            "amount": "0.00"
        }
    ]
}

    var configuration = {
      paymentMethodsResponse : availablePaymentMethods,
      clientKey: "test_RKKBP5GHOFFUFJJMJHOJAG7ZIIJKBMI6",
      locale: "en-US",
      showPayButton: true,
      environment: "test",
      

      paymentMethodsConfiguration: {
        applepay: applePayConfiguration,	      
   },
      
      onSubmit: (state,dropin)=>{
          //setTimeout(stopProcessing, 3000);
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
      onAdditionalDetails: (state,dropin)=>{
        //alert('onAdditionalDetails called.');
        $a_params = state.data;
        makeAdditionalDetails(state.data)
          .then(response => {
            var responseDetail = response.action;
            console.log(response);
            if(response.action) {
              //alert('action received.');
              dropin.handleAction(response.action);
              //show3DSResult(response);
            }
            else{
              show3DSResult(response);
            }
          })
          .catch(error => {
            console.log(error);
            throw Error(error);
          });
      },
      onClick: (resolve, reject) =>{
        alert('onClick Selected');
      },
      onAuthorized: (resolve, reject, event)=>{
	      alert('onAuthorized Selected');
      }
    }

    async function initialLoad(){
      const checkout = await AdyenCheckout(configuration);
      const dropin = checkout.create('applepay').mount('#kenjis-dropin');
      makeLister();
      //const dropin = checkout.create('card').mount('#kenjis-dropin');
    }	  

    function makeLister(){
      var applePayButton = document.getElementsByClassName('adyen-checkout__applepay__button');
      alert(applePayButton);
      alert(applePayButton[0]);
	    
    }

	  
  </script>
  <body onload="initialLoad()"><!--force to call initialLoad() function
    <img src="applepay_mufg.jpeg" alt="Italian Trulli">-->

    <h1></h1>
    <h2>ApplePay Component</h2>

    <div id="kenjis-dropin"></div><!--Put JS code in head becuase want to be ready for rendering the web object -->

  </body>
</html>
