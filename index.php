<?php

use App\Api;
use App\Database;

require_once 'vendor/autoload.php';
var_dump($_SERVER['REQUEST_URI']);
var_dump(getenv('DATABASE_URL'));
// Route to API.
if (preg_match('/^\/api\/.*/', $_SERVER['REQUEST_URI'])) {
    $db = new Database();
    $requestData = json_decode(file_get_contents('php://input'), TRUE) ?? [];
    $api = new Api($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $db, $requestData);
    $response = $api->getJsonResponse();

    header('Content-Type: application/json; charset=utf-8');
    echo $response;
}