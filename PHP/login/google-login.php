<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('13485856017-eak1pghsu6edesckr5vd31a5lb5toqv7.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/resortapp/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

$login_url = $client->createAuthUrl();

header("Location: $login_url");
exit;
