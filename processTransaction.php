<?php

declare(strict_types=1);

# if vendor file is not present, notify developer to run composer install.
require __DIR__ . '/vendor/autoload.php';

use HydrogenAfrica\Controller\PaymentController;
use HydrogenAfrica\EventHandlers\ModalEventHandler as PaymentHandler;
use HydrogenAfrica\HydrogenAfrica;
use HydrogenAfrica\Library\Modal;

# start a session.
session_start();

try {
    HydrogenAfrica::bootstrap();
    $customHandler = new PaymentHandler();
    $client = new HydrogenAfrica();
    $modalType = Modal::POPUP; // Modal::POPUP or Modal::REDIRECT
    $controller = new PaymentController($client, $customHandler, $modalType);
} catch (\Exception $e) {
    echo $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request = $_REQUEST;
    $request['redirect_url'] = $_SERVER['HTTP_ORIGIN'] . $_SERVER['REQUEST_URI'];
    try {
        $controller->process($request);
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}

$request = $_GET;
# Confirming Payment.
if (isset($request['tx_ref'])) {
    $controller->callback($request);
} else {
}
exit();
