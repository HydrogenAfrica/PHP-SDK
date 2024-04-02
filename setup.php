<?php  

use HydrogenAfrica\Helper;
use Dotenv\Dotenv;

$hydrogen_installation = 'composer';

if( !file_exists( '.env' )) {
    $dotenv = Dotenv::createImmutable(__DIR__."/../../../"); # on the event that the package is install via composer.
} else {
    $hydrogen_installation = "manual";
    $dotenv = Dotenv::createImmutable(__DIR__); # on the event that the package is forked or donwload directly from Github.
}

$dotenv->safeLoad();

//check if the current version of php is compatible
if(!Helper\CheckCompatibility::isCompatible())
{
    echo "Flutterwave: This SDK only support php version ". Helper\CheckCompatibility::MINIMUM_COMPATIBILITY. " or greater.";
    exit;
}

// check for required key in ENV super global
$hydrogenKeys = ["TEST_AUTH_TOKEN","LIVE_AUTH_TOKEN","MODE"];

asort($hydrogenKeys);

try{
    foreach($hydrogenKeys as $key)
    {
        if( empty( $_ENV[ $key ] ) )
        {
            throw new InvalidArgumentException("$key environment variable missing.");
        }
    }
}catch(\Exception $e)
{
    echo "<code>Hydrogen sdk: " .$e->getMessage()."</code>";
    echo "<br /> Kindly create a <code>.env </code> in the project root and add the required environment variables.";
    exit;
}

$keys = [
    'TEST_AUTH_TOKEN' => $_ENV['TEST_AUTH_TOKEN'],
    'LIVE_AUTH_TOKEN' => $_ENV['LIVE_AUTH_TOKEN'],
    'MODE' => $_ENV['MODE'],
    // 'TEST_AUTH_TOKEN' => "63C1A50C1B8186489CE6CDBC79D41E448A061698A798CB2E4D40E8C0DD829689",
    // 'LIVE_AUTH_TOKEN' => "4820FF2615DDA3E59C4DA00AA4D129382F91475B30B00C394EC5986CD134646E",
    // 'MODE' => "test",
];