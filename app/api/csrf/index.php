<?php
use App\Auth\HashAuth;
use App\Auth\Validation;

$methodsAllowed = [
	'get' => ['auth' => false]
];

$session = Validation::ModuleSecurity($methodsAllowed);
$remoteIPAddr = getUserIpAddr();
$hash = HashAuth::Create($remoteIPAddr);

return [
	'hash' => $hash,
	'remote_ip' => $remoteIPAddr
];
?>
