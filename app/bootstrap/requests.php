<?php
function postRequest() {
  /**
   * Obtiene el input en formato json y lo convierte en un objeto php
   */
  try {
    $params = json_decode(file_get_contents('php://input'));
  } catch (\Exception $e) {
    http_response_code(500);
    error_logs(['Clean data error', $e->getMessage()]);
    die(json_encode(['message' => 'Error while parsing body request']));
  }
  return $params;
}
?>
