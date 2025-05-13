<?php
require_once 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('13485856017-eak1pghsu6edesckr5vd31a5lb5toqv7.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/resortapp/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $oauth = new Google_Service_Oauth2($client);
    $userData = $oauth->userinfo->get();
    session_start();
    $_SESSION['email'] = $userData->email;
    $_SESSION['name'] = $userData->name;

    header("Location: dashboard.php");
    exit;
}
