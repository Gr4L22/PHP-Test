<?php

namespace App\Controllers;

class TokenController {

    public function login() { // Add here the variables to make the login user usable
        try {
            session_start();

            $tokenData = $this->getToken();

            if ($tokenData) {
                if (isset($tokenData['accessToken']) && isset($tokenData['exp'])) {
                    $_SESSION['sp_token'] = $tokenData['accessToken'];
                    $_SESSION['sp_token_expiry'] = $tokenData['exp'];
                    return $tokenData['accessToken'];
                } else {
                    $error_message = "Login error (getToken): " . json_encode($tokenData);
                    error_log($error_message);
                    throw new \Exception($error_message);
                }
            } else {
                return false;
            }
        } catch (\Exception $e) {
            error_log("Shipprimus Login Error: " . $e->getMessage());
            return false;
        }
    }

    private function getToken() {
        $url = "https://sandbox-api.shipprimus.com/api/v1/login";
        $username = "testDemo";
        $password = "1234";
        $payload = json_encode([
            "username" => $username,
            "password" => $password
        ]);

        $response = $this->makeRequest($url, $payload);

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            $error_message = "API error(getToken): " . json_encode($data);
            error_log($error_message);
            throw new \Exception($error_message);
        }

        if ($data && isset($data['data']) && isset($data['data']['accessToken']) && isset($data['data']['exp'])) {
            return $data['data'];
        } else {
            $error_message = "Bad response (getToken): " . json_encode($data);
            error_log($error_message);
            throw new \Exception($error_message);
        }
    }

    public function refreshtoken() {
        try {
            session_start();
            $currentToken = $_SESSION['sp_token'] ?? null;

            if (!$currentToken) {
                throw new \Exception('Missing token');
            }

            $url = "https://sandbox-api.shipprimus.com/api/v1/refreshtoken";
            $payload = json_encode([
                "token" => $currentToken
            ]);

            $response = $this->makeRequest($url, $payload);
            $data = json_decode($response, true);

            if (isset($data['error'])) {
                $error_message = "Refreshtoken API Errror: " . json_encode($data);
                error_log($error_message);
                throw new \Exception($error_message);
            }

            if ($data && isset($data['data']) && isset($data['data']['accessToken']) && isset($data['data']['exp'])) {
                $_SESSION['sp_token'] = $data['data']['accessToken'];
                $_SESSION['sp_token_expiry'] = $data['data']['exp'];
                return $data['data']['accessToken'];
            } else {
                $error_message = "API bad request (refreshtoken): " . json_encode($data);
                error_log($error_message);
                throw new \Exception($error_message);
            }
        } catch (\Exception $e) {
            error_log("Shipprimus Refresh Token Error: " . $e->getMessage());
            return false;
        }
    }

    private function makeRequest($url, $payload) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);

        if (curl_errno($ch)) {
            $error_message = "Error cURL: " . curl_error($ch);
            error_log($error_message);
            error_log("cURL info: " . print_r($info, true));
            throw new \Exception($error_message);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode != 200) {
            $error_message = "Error HTTP: " . $httpCode . " - " . $response;
            error_log($error_message);
            error_log("cURL info: " . print_r($info, true));
            throw new \Exception($error_message);
        }

        curl_close($ch);

        error_log("Request Info: " . print_r($info, true));
        error_log("Response: " . $response);

        return $response;
    }
}

?>