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
    "currency" => "USD",
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
  </head>
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

@media screen and (max-width: 400px) {
#float-box img,
#float-box p {
  float: none;
  max-width: 100%;
}
}
  </style>
  <script type="text/javascript">

    var availablePaymentMethods = JSON.parse( <?php echo $paymentmethodsrequestresponse; ?> );

    const idealConfiguration = {
        showImage: false, // Optional. Set to **false** to remove the bank logos from the iDEAL form.
        issuer: "1121", // Optional. Set this to an **id** of an iDEAL issuer to preselect it.
        placeholder: "KenjiBank"
    };

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
            window.location.href = 'http://127.0.0.1:8080/showResults.php';
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

    var configuration = {
      paymentMethodsResponse : availablePaymentMethods,
      clientKey: "test_RKKBP5GHOFFUFJJMJHOJAG7ZIIJKBMI6",
      locale: "en-US",
      showPayButton: true,
      environment: "test",
      hasHolderName: false,//added on Aug30
      holderNameRequired: false,//added on Aug30
      enableStoreDetails: false,//added on Aug30
      billingAddressRequired: false,//added on Aug30
      secondaryAmount: true,
      buttonType: "subscribe",
      buttonColor: "white-with-line",
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
      paymentMethodsConfiguration: {
          card:{
              hasHolderName: true,
              holderNameRequired: true,
              enableStoreDetails: true,
              name: 'Credit or debit card',
              billingAddressRequired: false
          },
          threeDS2: {
            challengeWindowSize: '05'
          },
      }
    }

    async function initialLoad(){
      const checkout = await AdyenCheckout(configuration);
      const dropin = checkout.create('applepay').mount('#kenjis-dropin');
      //const dropin = checkout.create('card').mount('#kenjis-dropin');
    }

  </script>
  <body onload="initialLoad()"><!--force to call initialLoad() function 
    <img src="applepay_mufg.jpeg" alt="Italian Trulli">-->

    <h1></h1>
    <h2>ApplePay Component</h2>

    <div id="kenjis-dropin"></div><!--Put JS code in head becuase want to be ready for rendering the web object -->

  </body>
</html>
