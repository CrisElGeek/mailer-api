<?php
use App\Auth\HashAuth;
use App\Auth\Validation;

$methodsAllowed = [
	'post' => ['auth' => true]
];
$hashString = $_GET['hash'];
$session = Validation::ModuleSecurity($methodsAllowed, $hashString);

require 'controller.php';

$method = REQUEST_METHOD;

$contact = new Contact();

if(method_exists($contact, $method)) {
	$response = $contact->$method();
} else {
	http_response_code(500);
	die(json_encode([
		'message' => 'Internal Server Error'
	]));
}
return $response;
?>
