<?php
	namespace App\Auth;
	
	abstract class Validation {
		static public function Uuid($uuid) {
			global $config;
			$regex = '[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}';
			$validated = false;
			if($uuid && preg_match('#' .$regex .'#', $uuid) && $uuid == $config['auth']['uuid']) {
				$validated = true;
			}
			return $validated;
		}

		static private function Methods($methods) {
			if(empty($methods)) {
				throw new \Exception('No allowed methods given', 405);
			} elseif(!array_key_exists(REQUEST_METHOD, $methods)) {
				throw new \Exception('Request method not allowed', 405);
			}
		}

		static public function ModuleSecurity(array $methods, $hashString = NULL) {
			try {
				self::Methods($methods);
			} catch(\Exception $e) {
				http_response_code(405);
				die(json_encode([
					'message'	=> 'Method not allowed'
				]));
			}

			$m = $methods[REQUEST_METHOD];

			if($m['auth'] == true && $hashString) {
				if(HashAuth::Validate($hashString, $_SERVER['REMOTE_ADDR']) != true) {
					http_response_code(401);
					die(json_encode([
						'message'	=> 'No acccess allowed'
					]));
				}
			} elseif($m['auth'] == true && $hashString == NULL) {
				http_response_code(401);
				die(json_encode([
					'message'	=> 'No acccess allowed'
				]));
			}
			return true;
		}
	}
?>
