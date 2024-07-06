<?php

declare(strict_types=1);

namespace Bitrix\Ikko\Controller;

use Bitrix\Ikko\Configuration\Config;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class Ikko extends \Bitrix\Main\Engine\Controller
{
	protected HttpClient $httpClient;
	public function postMenuAction()
	{
		$response = $this->httpClient->post(Config::getIkkoUrl());
		$data = Json::decode($response);
		$menu = $data['menu'];
	}

	protected function init(): void
	{
		parent::init();

		$this->httpClient = new HttpClient();
	}
}