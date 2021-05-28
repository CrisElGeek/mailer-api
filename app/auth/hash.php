<?php
namespace App\Auth;
/**
 * Autenticacion por medio de un hash utilizable en enlaces el cual permite que solo quien cuente con el enlace pueda ver la informacion proporcionada, este es un hash unico y solo funciona con el enlace proporcionado
 **/
abstract class HashAuth {
	/**
	 * Se crea un hash utilizando la funcion de php password_hash()
	 * El HASH_AUTH_PASS es un tipo de password en una variable, este es fijo y no debe ser compartido, al igual que un password debe ser lo suficientemente seguro.
	 * El hash string puede ser un id de usuario o pedido u otro
	 * Se retira del string resultante la parte inicial que siempre es igual en los generados por password_hash() para de este modo darle mas seguridad al hash y no pueda ser identificada la forma de encriptacion
	 * Finalmente se pasa por la funcion base64_encode() para que los caracteres sean mas amigables con la url
	 */
	public static function Create ($hashString) {
		$hash = password_hash($GLOBALS['config']['auth']['hash'] . $hashString, PASSWORD_BCRYPT);
		$string = preg_replace('/^\$2y\$10\$/', '', $hash);
		$encoded = base64_encode($string);
		$str = trim($encoded, '=');
		return $str;
	}

	public static function Validate($string, $hashString) {
		$s = base64_decode($string);
		$hash = '$2y$10$' .$s;
		return password_verify($GLOBALS['config']['auth']['hash'] . $hashString, $hash);
	}
}
?>
