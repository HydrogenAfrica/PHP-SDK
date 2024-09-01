<?php  

use HydrogenpayAfrica\Helper;
use Dotenv\Dotenv;

$hydrogenpay_installation = 'composer';

$dotenvPath = __DIR__ . "/../../../";
if (file_exists($dotenvPath . '.env')) {
    $dotenv = Dotenv::createImmutable($dotenvPath);
} else {
    $dotenvPath = __DIR__;
    if (file_exists($dotenvPath . '/.env')) {
        $dotenv = Dotenv::createImmutable($dotenvPath);
    } else {
        echo "Environment (.env) variable missing.";
        exit; // This will prevent further execution
    }
}

$dotenv->safeLoad();

//check if the current version of php is compatible
if(!Helper\CheckCompatibility::isCompatible())
{
    echo "Flutterwave: This SDK only support php version ". Helper\CheckCompatibility::MINIMUM_COMPATIBILITY. " or greater.";
    exit;
}

// check for required key in ENV super global
$hydrogenpayKeys = ["TEST_AUTH_TOKEN","LIVE_AUTH_TOKEN","MODE"];

asort($hydrogenpayKeys);

try{
    foreach($hydrogenpayKeys as $key)
    {
        if( empty( $_ENV[ $key ] ) )
        {
            throw new InvalidArgumentException("$key environment variable missing.");
        }
    }
}catch(\Exception $e)
{
    echo "<code>Hydrogenpay sdk: " .$e->getMessage()."</code>";
    echo "<br /> Kindly create a <code>.env </code> in the project root and add the required environment variables.";
    exit;
}

$keys = [
    'TEST_AUTH_TOKEN' => $_ENV['TEST_AUTH_TOKEN'],
    'LIVE_AUTH_TOKEN' => $_ENV['LIVE_AUTH_TOKEN'],
    'MODE' => $_ENV['MODE'],
];
