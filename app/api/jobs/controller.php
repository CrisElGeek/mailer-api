<?php
use App\Helpers\Mailer;

class Jobs {
	function __construct($request) {
		$this->request = $request;
		$this->subject = 'Nueva solicitud de trabajo enviada';
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
				[
					'email' => $GLOBALS['config']['recipients']['jobs']['email'],
					'name' => $GLOBALS['config']['recipients']['jobs']['sender']
				]
			],
			'attachments' => json_decode($this->request->attachments)
		];
		try {
			Mailer::Send($data);
		} catch(\Exception $e) {
			http_response_code(500);
			error_logs(['Error al enviar el mensaje', $e->getMessage(), json_encode($this->request)]);
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
		$terms = $this->request->terms ? 'Si' : 'No';
		$area = $this->request->area;
		$city = $this->request->city;
		$state = $this->request->state;
		$phone = $this->request->phone;
		$b =
<<<HTML
	<body>
		<ul>
			<li>Nombre: $name</li>
			<li>Correo: $email</li>
			<li>Asunto: $this->subject</li>
			<li>Teléfono: $phone</li>
			<li>Ciudad: $city</li>
			<li>Estado: $state</li>
			<li>Área de interés: $area</li>
			<li>Acepta términos y condiciones: $terms</li>
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
