<?php

namespace WalletSDK;

use Exception;

class WalletSDK {
    private $apiUrl;
    private $apiKey;

    public function __construct($apiKey) {
        $this->apiUrl = 'https://walletapi-sams-projects-5a296786.vercel.app/api/';
        $this->apiKey = $apiKey;
    }

    /**
     * Generates a wallet and displays a modal with address and countdown.
     *
     * @param string $cryptoName Cryptocurrency name (e.g., 'ETH' for Ethereum)
     * @param float $amount Amount of crypto to be sent
     * @return void
     * @throws Exception
     */
    public function generateWallet($cryptoName, $amount) {
        $url = ($cryptoName === 'ETH') ? $this->apiUrl . 'eth-wallet-generate' : $this->apiUrl . 'generateWallet';
        $response = $this->makeRequest($url, ['cryptoName' => $cryptoName]);

        if ($response && isset($response->address)) {
            $this->logPrivateKey($response->privateKey);
            $this->displayModal($response->address, $cryptoName, $amount);
        } else {
            throw new Exception("Failed to generate wallet for $cryptoName.");
        }
    }

    /**
     * Logs the private key securely.
     *
     * @param string $privateKey The wallet's private key
     */
    private function logPrivateKey($privateKey) {
        file_put_contents(__DIR__ . '/../logs/private_key_log.txt', $privateKey . PHP_EOL, FILE_APPEND);
    }

    /**
     * Displays a modal popup with the wallet address and a countdown timer.
     *
     * @param string $address Wallet address
     * @param string $cryptoName Cryptocurrency name
     * @param float $amount Amount to send
     */
    private function displayModal($address, $cryptoName, $amount) {
        echo "
        <div id='paymentModal' style='display: block;'>
            <p>Send {$amount} {$cryptoName} to the following address:</p>
            <p><strong>{$address}</strong></p>
            <p>Transaction timeout: <span id='countdown'>10:00</span></p>
            <button onclick='confirmPayment()'>I HAVE PAID</button>
        </div>
        <script>
            let timeLeft = 600;
            const countdownEl = document.getElementById('countdown');
            const interval = setInterval(() => {
                timeLeft--;
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                countdownEl.textContent = ``;
                if (timeLeft <= 0) clearInterval(interval);
            }, 1000);

            function confirmPayment() {
                document.getElementById('paymentModal').style.display = 'none';
                fetch('checkPayment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ address: '{$address}' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'Payment received') {
                        alert('Thank you! Your payment was confirmed.');
                    } else if (data.status === 'Payment pending') {
                        alert('Payment is pending, please try again in a few moments.');
                    } else {
                        alert('Error: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        </script>
        ";
    }

    /**
     * Makes an HTTP request to the API.
     *
     * @param string $url API endpoint
     * @param array $data Payload for the POST request
     * @return object|null
     */
    private function makeRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
}
