<?php namespace AK\Commweb;

abstract class RequestAbstract implements \JsonSerializable {

	private $method = 'PUT';
	private $version = 34;
	private $url = "https://paymentgateway.commbank.com.au/api/rest/";

	private $merchant;
	private $password;
	private $testMode = false;

	protected $apiOperation;

	protected $order;
	protected $transaction;

	public function __construct($merchant) {

		$this->setMerchant($merchant);
	}

	public function setTransaction($transaction) {

		$this->transaction = $transaction;
	}

	public function setOrder($order) {

		$this->order = $order;
	}

	public function setMethod($method) {

		$this->method = $method;
	}

	public function setVersion($version) {

		$this->version = $version;
	}

	public function setUrl($url) {

		$this->url = $url;
	}

	public function setMerchant($merchant) {

		if (substr($merchant, 0, 4) == "TEST") {

			$this->setTestMode(true);
			$this->merchant = substr($merchant, 4, strlen($merchant) - 4);
		} else {

			$this->setTestMode(false);
			$this->merchant = $merchant;
		}
	}

	public function getMerchant() {

		return ($this->testMode) ? ("TEST" . $this->merchant) : $this->merchant;
	}

	public function setApiPassword($password) {

		$this->password = $password;
	}

	public function setTestMode($testMode) {

		$this->testMode = $testMode;
	}

	public function getTestMode() {

		return $this->testMode;
	}

	public function send() {

		$client = new \GuzzleHttp\Client();
		$res = $client->request(
			$this->method,
			$this->getApiUrl(),
			[
				"auth" => [$this->getApiUsername(), $this->getApiPassword()],
				"json" => $this
			]
		);

		return $this;
	}

	public function getApiUsername() {

		return "merchant." . $this->getMerchant();
	}

	public function getApiPassword() {

		return $this->password;
	}

	private function getApiUrl() {

		$url = $this->url;
		$url = $url . "version/" . $this->version . "/";
		$url = $url . "merchant/" . $this->getMerchant() . "/";
		$url = $url . "order/" . $this->order->getId() . "/";
		$url = $url . "transaction/" . $this->transaction->getId() . "/";

		return $url;
	}
}