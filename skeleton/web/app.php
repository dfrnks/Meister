<?php

// Allow CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    $headers=getallheaders();
    @$ACRH=$headers["Access-Control-Request-Headers"];
    header("Access-Control-Allow-Headers: $ACRH");
}

require_once __DIR__ . '/../app/bootstrap.php';

$app = new \app\AppInit('DEV');

$app->Run();