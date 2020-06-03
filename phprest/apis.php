<?php

include('./httpful.phar');

// x-auth-token
//$apikey = 'c9Y$oNF>"9CEv/>H%F_,';
$apikey = 'KX^$dcvAzDMVYB4ur?$P';

// PUT 
$sessionApi = 'https://apis.uloz.to/session/v1';


$response = \Httpful\Request::put($sessionApi)
    ->withXAuthToken($apikey)
    ->sendsJson()
    ->expectsJson()
    ->body('{"login": "revelon", "password": "xxrevelon123", "device_id": "sDEVI543dfdfCEID12345456456456"}')
    ->send();

var_dump($response);