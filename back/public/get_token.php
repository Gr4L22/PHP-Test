<?php
require_once '../vendor/autoload.php';

use App\Controllers\TokenController;

$tokenController = new TokenController();
$token = $tokenController->login();

echo $token;
?>