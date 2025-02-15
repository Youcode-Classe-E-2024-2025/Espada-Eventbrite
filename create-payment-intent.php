<?php
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_XXXXXXXXXXXXXXXXXXXXXXXX'); // Replace with your test secret key

header('Content-Type: application/json');

try {
    // Create a PaymentIntent
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => 1000, // Amount in cents (e.g., $10.00 = 1000)
        'currency' => 'usd',
        'payment_method_types' => ['card'],
    ]);

    // Return the client secret to the frontend
    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret,
    ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    // Handle errors
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
