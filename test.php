<?php
$username = '1';
$password = '123456';

$url = "http://106.15.139.140/wp-json/jwt-auth/v1/token";
$data = json_encode(['username' => $username, 'password' => $password]);

$options = ['http' => [
    'method'  => 'POST',
    'header'  => "Content-Type: application/json\r\n",
    'content' => $data,
    'ignore_errors' => true,
]];
$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
var_dump($result);
