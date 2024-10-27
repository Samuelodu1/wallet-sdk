<?php

require_once __DIR__ . '/vendor/autoload.php';

use WalletSDK\WalletSDK;

// Retrieve data from the POST request
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['address'])) {
    echo json_encode(['error' => 'Wallet address is required']);
    exit;
}

// Use your API key here
$apiKey = 'YOUR_API_KEY';
$walletSDK = new WalletSDK($apiKey);

// Define the function to check payment
function checkPaymentStatus($walletSDK, $address) {
    $apiUrl = 'https://walletapi-sams-projects-5a296786.vercel.app/api/check-transaction'; // Sample API for checking transactions

    try {
        $response = $walletSDK->makeRequest($apiUrl, ['address' => $address]);

        if ($response && isset($response->status)) {
            if ($response->status === 'confirmed') {
                echo json_encode(['status' => 'Payment received']);
            } else {
                echo json_encode(['status' => 'Payment pending']);
            }
        } else {
            echo json_encode(['error' => 'Unable to check payment status']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
}

checkPaymentStatus($walletSDK, $data['address']);

