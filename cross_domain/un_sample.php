<?php

// header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Origin: http://b.com');
header('Access-Control-Allow-Credentials: true');

header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
// header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Headers: Content-Type ");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    die;
}


var_dump($_COOKIE);


// session_set_cookie_params(0, '/', '.a.com');
session_start();

$result = [
    'code' => 1,
    'msg' => 'ok'
];

echo json_encode($result);die;
