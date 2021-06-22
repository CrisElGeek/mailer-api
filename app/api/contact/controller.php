<?php
use App\Helpers\Mailer;

class Contact {
	function __construct($request) {
		$this->request = $request;
		$this->subject = 'Mensaje de contacto desde la página web de Adelnor';
	}

	public function post() {
		$html = $this->HTML();
		$data = [
			'subject' => $this->subject,
			'body' => $html,
			'alt_body' => $this->HTML(),
			'from' => [
				'email' => $this->request->email,
				'name' => $this->request->name
			],
			'to' => [
				$GLOBALS['config']['recipients']['contact']['to']
			],
			'cc' => [
				$GLOBALS['config']['recipients']['contact']['cc']
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
		$this->subject = $this->request->subject ?: $this->subject;
		$ip = $_SERVER['REMOTE_ADDR'];
		$datetime = date('d-m-Y H:i:s');
		$body = $this->request->message;
		$newsletter = $this->request->newsletter ? 'Si' : 'No';
		$b =
<<<HTML
	<body>
		<ul>
			<li>Nombre: $name</li>
			<li>Correo: $email</li>
			<li>Asunto: $this->subject</li>
			<li>Quiere subscribirse al newsletter: $newsletter</li>
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
