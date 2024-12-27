<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\TokenController;

if (isset($_GET['route'])) {
    $route = $_GET['route'];
    switch ($route) {
        case 'login':
            $controller = new TokenController();
            $token = $controller->login();
            header('Content-Type: application/json');
            echo json_encode(['token' => $token]);
            exit;
        case 'obtainInfo':
        case 'transformInfo':
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
            if ($authHeader) {
                if ($route == 'transformInfo') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    echo "Transformed info: " . strtoupper($data['data']);
                } else {
                    echo "Some info!";
                }
            } else {
                http_response_code(401);
                echo "Unauthorized";
            }
            exit;
        default:
            http_response_code(404);
            echo "Route not found";
            exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Get Rates</title>
</head>
<body>
    <h1>Get Rates</h1>

    <form id="rateForm">
        <label for="vendorId">Vendor ID: 1901539643</label>
        <!-- <input type="number" id="vendorId" name="vendorId" value="1"><br><br> -->
        <button type="button" id="getToken">Get Token</button>
        <button type="button" id="obtainInfo" disabled>Get & process Info</button>
    </form>

    <div id="results"></div>

    <script>
        let token = null;

        document.getElementById('getToken').addEventListener('click', function() {
            fetch('get_token.php')
                .then(response => response.text())
                .then(data => {
                    token = data;
                    if (token) {
                        alert("Token obtained: " + token.substring(0, 20) + "...");
                        document.getElementById('obtainInfo').disabled = false;
                    } else {
                        alert("Error getting token.");
                    }
                })
                .catch(error => console.error('Error:', error));
        });

        document.getElementById('obtainInfo').addEventListener('click', function() {
            if (!token) {
                alert("You must get a token first.");
                return;
            }

            // const vendorId = document.getElementById('vendorId').value;
            const vendorId = 1901539643;
            fetch(`get_rates.php?vendorId=${vendorId}`, { 
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            })
            .then(response => response.json())
            .then(data => {
                let resultsDiv = document.getElementById('results');
                resultsDiv.innerHTML = '';
                if (data) {
                    if (data.error) {
                        resultsDiv.innerHTML = `<p style="color: red;">Error: ${data.error}</p>`;
                    } else if (data.message){
                        resultsDiv.innerHTML = `<p style="color:red;">Message: ${data.message}</p>`;
                    }
                    else if (Array.isArray(data)){
                        data.forEach(rate => {
                            resultsDiv.innerHTML += `<pre>${JSON.stringify(rate, null, 2)}</pre>`;
                        });
                    } else {
                        resultsDiv.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                    }
                } else {
                    resultsDiv.innerHTML = "<p>Error getting data.</p>";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('results').innerHTML = "<p style='color: red;'>Request error.</p>";
            });
        });
    </script>
</body>
</html>