<?php

$url = "https://checkout-test.adyen.com/v66/payments";

$payments_data = $_POST;

//echo $payments_data;

$additional_data = [
    //'reference' => 'KenjiW001',
    'reference' => 'ApplePay_0001',
    'merchantAccount' => 'KenjiW',
    'amount' => [
        'value' => 100,
        'currency' => 'USD'
    ],
    'returnUrl' => 'http://127.0.0.1:8080/return.php',
    'channel' => 'Web',
    'additionalData' => [
        'allow3DS2' => 'true'
    ],
    'origin' => 'http://127.0.0.1:8080',
    //'paymentMethod' => $payments_data->PaymentMethod,
    'browserInfo' => [
      'userAgent' => 'Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/70.0.3538.110 Safari\/537.36',
      'acceptHeader' => "text\/html,application\/xhtml+xml,application\/xml;q=0.9,image\/webp,image\/apng,*\/*;q=0.8",
      "language" => "en-US",
      "colorDepth" => 24,
        "screenHeight" => 723,
        "screenWidth" => 1536,
        "timeZoneOffset" => 0,
      "javaEnabled" => true
    ]
];

$final_payment_data = array_merge($payments_data, $additional_data);

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
        CURLOPT_POSTFIELDS     => json_encode($final_payment_data),
        CURLOPT_HTTPHEADER     => $curl_http_header,
        CURLOPT_VERBOSE        => true
    ]
);

$payments_response = curl_exec($curl);
$file = 'paymentsCallResponse.txt';
$current = $payments_response;
file_put_contents($file, $current);

header('Content-Type: application/json');
echo $payments_response;

curl_close($curl);
