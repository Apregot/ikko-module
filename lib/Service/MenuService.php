<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Service;

use Bitrix\Ikkomodule\Configuration\Config;
use Bitrix\Ikkomodule\Model\Menu;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class MenuService
{
	protected HttpClient $httpClient;

	public function __construct()
	{
		$this->init();
	}

	public function fetch(): Menu
	{
		$response = $this->httpClient->post(Config::getIkkoUrl() . '/api/menu/list');
		$data = Json::decode($response);

		return Menu::createFromArray($data);
	}

	protected function init(): void
	{
		$this->httpClient = new HttpClient();
	}
}