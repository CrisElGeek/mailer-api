<?php
use App\Auth\HashAuth;
use App\Auth\Validation;

$methodsAllowed = [
	'post' => ['auth' => true]
];
$hashString = $bodyRequest->hash;
$session = Validation::ModuleSecurity($methodsAllowed, $hashString);

require 'controller.php';

$method = REQUEST_METHOD;

$jobs = new Jobs($bodyRequest);

if(method_exists($jobs, $method)) {
	$response = $jobs->$method();
} else {
	http_response_code(500);
	error_logs(['El metodo solicitado no esta incorporado a la clase de este modulo', $method]);
	die(json_encode([
		'message' => 'Internal Server Error'
	]));
}
return $response;
?>
