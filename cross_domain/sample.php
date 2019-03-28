<?php

// header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Origin: http://b.com');
header('Access-Control-Allow-Credentials: true');

var_dump($_COOKIE);


$result = [
    'code' => 1,
    'msg' => 'ok'
];


// session_set_cookie_params(0, '/', '.a.com');
session_start();

echo json_encode($result);die;
