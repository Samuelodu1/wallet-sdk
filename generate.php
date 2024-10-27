<?php
require_once 'vendor/autoload.php';

use WalletSDK\WalletSDK;

$walletSDK = new WalletSDK('YOUR_API_KEY');
$walletSDK->generateWallet('ETH', 0.01); // Generates a wallet for Ethereum