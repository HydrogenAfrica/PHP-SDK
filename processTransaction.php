<?php

declare(strict_types=1);

# if vendor file is not present, notify developer to run composer install.
require __DIR__.'/vendor/autoload.php';

use HydrogenpayAfrica\Controller\PaymentController;
use HydrogenpayAfrica\EventHandlers\ModalEventHandler as PaymentHandler;
use HydrogenpayAfrica\HydrogenpayAfrica;
use HydrogenpayAfrica\Library\Modal;

# start a session.
session_start();

try {
    HydrogenpayAfrica::bootstrap();
    $customHandler = new PaymentHandler();
    $client = new HydrogenpayAfrica();
    $modalType = Modal::REDIRECT; // Modal::POPUP or Modal::REDIRECT
    $controller = new PaymentController( $client, $customHandler, $modalType );
} catch(\Exception $e ) {
    echo $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request = $_REQUEST;
    $request['redirect_url'] = $_SERVER['HTTP_ORIGIN'] . $_SERVER['REQUEST_URI'];
    try {
        $controller->process( $request );
    } catch(\Exception $e) {
        echo $e->getMessage();
    }
}

$request = $_GET;
# Confirming Payment.
if(isset($request['TransactionRef'])) {
    $controller->callback( $request );
} else {
    
}
exit();