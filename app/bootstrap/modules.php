<?php
function apiModules() {
  /**
   * Valida que el modulo exista y lo llama
   */
	if(isset($_GET['m'])) {
		$module_name = trim(strip_tags($_GET['m']));
  	$module      = __DIR__ . '/../api/' . $module_name;

		if (!empty($module_name) && is_dir($module)) {
			define('MODULE', $module_name);
    	return $module .'/index.php';
  	} else {
    	http_response_code(404);
    	error_logs(['NO MODULE', 404, 'No module found', $module_name]);
    	die(json_encode(['message' => 'Module not found', 'module' => $module_name]));
		};
	} else {
		http_response_code(404);
    error_logs(['NO MODULE', 404, 'No module found', $module_name]);
    die(json_encode(['message' => 'Module not found', 'module' => $module_name]));
	}
}
?>
