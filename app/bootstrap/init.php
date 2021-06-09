<?php
require __DIR__ . '/../extensions/vendor/autoload.php';
require __DIR__ . '/../config/base.php';
require __DIR__ . '/modules.php';
require __DIR__ . '/../libs/error.log.php';
require __DIR__ . '/../libs/functions.php';
require __DIR__ . '/requests.php';
require __DIR__ . '/../auth/hash.php';
require __DIR__ . '/../auth/validation.php';
require __DIR__ . '/../helpers/mailer.php';

$bodyRequest = NULL;
define('REQUEST_METHOD', strtolower($_SERVER['REQUEST_METHOD']));
if(REQUEST_METHOD === 'post') {
	$bodyRequest = postRequest();
}

$moduleDir = apiModules();

$response = require $moduleDir;

echo json_encode([
	'data'	=> $response
]);
?>
