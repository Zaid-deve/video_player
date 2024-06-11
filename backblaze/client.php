<?php

// includes
require_once "vendor/autoload.php";

use BackblazeB2\Client;

class ClientFactory
{
    private static $applicationKeyId = '';
    private static $applicationKey = '';

    public static function getClient()
    {
        return new Client(self::$applicationKeyId, self::$applicationKey);
    }
}

$client = ClientFactory::getClient();
