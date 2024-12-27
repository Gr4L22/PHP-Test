<?php
require_once '../vendor/autoload.php';

use App\Controllers\RateController;

header('Content-Type: application/json');

$rateController = new RateController();
$vendorId = isset($_GET['vendorId']) ? $_GET['vendorId'] : 1901539643; //vendorId include ass parameter for previus iteration
$rates = $rateController->getRates($vendorId);

echo json_encode($rates);
?>