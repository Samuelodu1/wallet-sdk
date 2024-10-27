<?php

require_once __DIR__ . '/../vendor/autoload.php';

use WalletSDK\WalletSDK;

$walletSDK = new WalletSDK('YOUR_API_KEY');
$walletSDK->generateWallet('ETH', 0.01); // Generates wallet for Ethereum
