<?php
use App\Helpers\Mailer;

class Contact {
	function __construct($request) {
		$this->request = $request;
	}

	public function post() {
		$data = [
			'subject' => $this->request->subject,
			'body' => $this->HTML(),
			'alt_body' => $this->HTML(),
			'from' => [
				'email' => $this->request->email,
				'name' => $this->request->name
			],
			'to' => [
				[
					'email' => $GLOBALS['config']['mailer']['email'],
					'name' => $GLOBALS['config']['mailer']['sender']
				]
			],
			'attachments' => json_decode($this->request->attachments)
		];
		try {
			Mailer::Send($data);
		} catch(\Exception $e) {
			http_response_code(500);
			error_logs(['Error al enviar el mensaje de contacto', $e->getMessage(), json_encode($this->request)]);
			die(json_encode([
				'message' => $e->getMessage()
			]));
		}
		return [
			'message' => 'Message sent!'
		];
	}

	private function HTML() {
		$email = $this->request->email;
		$name = $this->request->name;
		$subject = $this->request->subject;
		$ip = $_SERVER['REMOTE_ADDR'];
		$datetime = date('d-m-Y H:i:s');
		$body = $this->request->message;
		$b =
<<<HTML
	<body>
		<ul>
			<li>Nombre: $name</li>
			<li>Correo: $email</li>
			<li>Asunto: $subject</li>
			<li>Dirección IP: $ip</li>
			<li>Fecha y hora: $datetime</li>
		</ul>
		<p>$body</p>
	</body>
HTML;
	return $b;
	}
}
?>
