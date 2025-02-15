<?php
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_XXXXXXXXXXXXXXXXXXXXXXXX'); // Replace with your test secret key

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$endpoint_secret = 'whsec_XXXXXXXXXXXXXXXX'; // Replace with your webhook secret

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

// Handle the event
switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object;
        error_log('PaymentIntent succeeded: ' . $paymentIntent->id);
        break;
    case 'payment_intent.payment_failed':
        $paymentIntent = $event->data->object;
        error_log('PaymentIntent failed: ' . $paymentIntent->id);
        break;
    default:
        error_log('Unhandled event type: ' . $event->type);
}

http_response_code(200);
