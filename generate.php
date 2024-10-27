<?php
require 'vendor/autoload.php';

use WalletSDK\WalletSDK;

$sdk = new WalletSDK('your-api-key');
$sdk->generateWallet('ETH', 1.5);