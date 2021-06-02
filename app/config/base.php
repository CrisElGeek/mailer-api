<?php
use Symfony\Component\Yaml\Yaml;

// VARIABLES RESERVADAS
global $bodyRequest;

$GLOBALS['config'] = Yaml::parseFile(__DIR__ . '/config.yml', 2, 4, Yaml::PARSE_OBJECT);

ini_set('session.use_cookies', 0);

date_default_timezone_set($config['timezone']);

ini_set('log_errors', 1);
ini_set('display_errors', $config['debug']);
ini_set('display_startup_errors', $config['debug'] );
error_reporting(E_ALL	& ~E_NOTICE);
/**
 * Configuracion de los headers del API
 */
header('Access-Control-Allow-Headers: X-Requested-With, Authorization, Content-Type, X-PINGOTHER, X-Identifier');

if ($config['cors']['active']) {
  header('Access-Control-Allow-Origin: *');
} else {
  $http_origin = $_SERVER['HTTP_ORIGIN'];
  if (in_array($http_origin, $config['cors']['domains'])) {
    header('Access-Control-Allow-Origin: ' . $http_origin);
  }
}

header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header('Pragma: no-cache');
header('Content-Type: application/json; charset=utf8mb4');
header("P3P: CP='IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA'");
header('Access-Control-Allow-Credentials: true');

define('DEBUG_LOG_FILE', __DIR__ .'/../logs/debug.log');
define('STATICS_DIR', $_SERVER['DOCUMENT_ROOT'] .'/statics/');
?>
