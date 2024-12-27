<?php

namespace App\Controllers;

class RateController {

    private $tokenController;

    public function __construct() {
        $this->tokenController = new TokenController();
    }

    public function getRates($vendorId) { //vendorId include ass parameter for previus iteration
        session_start();
        if (!isset($_SESSION['sp_token']) || !isset($_SESSION['sp_token_expiry'])) {
            error_log("No token or expiry found.");
            return false;
        }
        $token = $_SESSION['sp_token'];
        $expiry = $_SESSION['sp_token_expiry'];
        if ($expiry <= time()) {
            error_log("Token expired. Trying to refresh.");
            $newToken = $this->tokenController->refreshtoken();
            if ($newToken) {
                $token = $newToken;
                $_SESSION['sp_token'] = $token;
            } else {
                error_log("Could not refresh token.");
                return false;
            }
        }
        $originCity = "KEY LARGO";
        $originState = "FL";
        $originZipcode = "33037";
        $originCountry = "US";
        $destinationCity = "LOS ANGELES";
        $destinationState = "CA";
        $destinationZipcode = "90001";
        $destinationCountry = "US";
        $UOM = "US";
        $freightInfo = [
            [
                "qty" => 1,
                "weight" => 100,
                "weightType" => "each",
                "length" => 40,
                "width" => 40,
                "height" => 40,
                "class" => 100,
                "hazmat" => 0,
                "commodity" => "",
                "dimType" => "PLT",
                "stack" => false
            ]
        ];
        $url = "https://sandbox-api.shipprimus.com/api/v1/database/vendor/contract/$vendorId/rate";
        $payload = http_build_query([
            "originCity" => $originCity,
            "originState" => $originState,
            "originZipcode" => $originZipcode,
            "originCountry" => $originCountry,
            "destinationCity" => $destinationCity,
            "destinationState" => $destinationState,
            "destinationZipcode" => $destinationZipcode,
            "destinationCountry" => $destinationCountry,
            "UOM" => $UOM,
            "freightInfo" => json_encode($freightInfo)
        ]);
        $response = $this->makeApiRequest($url, $token, $payload);
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['error'])) {
                error_log("Shipprimus API Error: " . json_encode($data));
                return false;
            } else {
                return $this->formatRates($data);
            }
        } else {
            error_log("Error calling Shipprimus API.");
            return false;
        }
    }

    private function formatRates($apiResponse) {
        if (!isset($apiResponse['data']['results']) || !is_array($apiResponse['data']['results'])) {
            return [];
        }

        $formattedRates = [];
        foreach ($apiResponse['data']['results'] as $rate) {
            $formattedRates[] = [
                "CARRIER" => "Rate " . substr($rate['id'], 0, 3) . " " . $rate['name'],
                "SERVICE LEVEL" => $rate['serviceLevel'],
                "RATE TYPE" => $rate['rateType'],
                "TOTAL" => "$" . number_format($rate['total'], 2),
                "TRANSIT TIME" => $rate['transitDays'] . " days"
            ];
        }
        return $formattedRates;
    }

    private function makeApiRequest($url, $token, $payload) {
        $ch = curl_init($url . '?' . $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_message = "CURL ERROR: " . curl_error($ch);
            error_log($error_message);
            throw new \Exception($error_message);
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode != 200) {
            $error_message = "HTTP ERROR: " . $httpCode . " - " . $response;
            error_log($error_message);
            throw new \Exception($error_message);
        }
        curl_close($ch);
        return $response;
    }
}