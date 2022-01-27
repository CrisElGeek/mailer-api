<?php
use MailchimpMarketing\ApiClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class MailChimp {
	private $client;
	function __construct($request) {
		global $config;
		$this->request = $request;
		$cnf = $config['mailchimp'];
		$this->config = $cnf;
		$this->client = new ApiClient();
		$this->client->setConfig([
			'apiKey' => $cnf['key'],
			'server' => $cnf['server']
		]);
	}

	public function post() {
		try {
			$response = $this->client->lists->addListMember($this->config['list_id'], [
				"email_address" => $this->request->email,
				"status" => "subscribed",
			]);
		} catch(\InvalidArgumentException $e) {
			http_response_code(500);
			error_logs(['Mailchimp', $e->getMessage(), __FILE__, __LINE__]);
			die(json_encode([
				"response" => $e->getMessage()
			]));
		} catch(ClientException $e) {
			http_response_code(500);
			$r =	$e->getResponse()->getBody()->getContents();
			error_logs(['Mailchimp', $r, __FILE__, __LINE__]);
			die(json_encode([
				"response" => json_decode($r)
			]));
		} catch(\Exception $e) {
			http_response_code(500);
			error_logs(['Mailchimp', $e->getMessage(), __FILE__, __LINE__]);
			die(json_encode([
				"response" => $e->getMessage()
			]));
		}
		return $response;
	}

	public function get() {
		try {
			$response = $this->client->lists->getAllLists();
		} catch(ConnectException $e) {
			http_response_code(500);
			$r =	$e->getResponse()->getBody()->getContents();
			error_logs(['Mailchimp', $r, __FILE__, __LINE__]);
			die(json_encode([
				"response" => json_decode($r)
			]));
		} catch(ClientException $e) {
			http_response_code(500);
			error_logs(['Mailchimp', $e->getMessage(), __FILE__, __LINE__]);
			die(json_encode([
				"response" => $e->getMessage()
			]));
		}
		return $response;
	}
}
?>
