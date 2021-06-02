<?php
use App\Auth\HashAuth;
use App\Auth\Validation;

$methodsAllowed = [
	'post' => ['auth' => true]
];
$hashString = $_POST['hash'];
$session = Validation::ModuleSecurity($methodsAllowed, $hashString);

require 'controller.php';

$method = REQUEST_METHOD;

$upload = new Upload();

if(method_exists($upload, $method)) {
	$response = $upload->$method();
} else {
	http_response_code(500);
	error_logs(['El metodo solicitado no esta incorporado a la clase de este modulo', $method]);
	die(json_encode([
		'message' => 'Internal Server Error'
	]));
}
return $response;
?>
