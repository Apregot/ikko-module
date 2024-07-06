<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Service;

use Bitrix\Ikkomodule\Configuration\Config;
use Bitrix\Ikkomodule\Model\DetailItem;
use Bitrix\Ikkomodule\Model\Menu;
use Bitrix\Ikkomodule\Model\MenuItem;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class DetailService
{
	protected HttpClient $httpClient;

	public function __construct()
	{
		$this->init();
	}

	public function fetch(int $id): DetailItem
	{
		$r = Json::encode(['id' => $id,]);
		$response = $this->httpClient->post(Config::getIkkoUrl() . '/api/item/get', $r);

		$data = Json::decode($response);

		return DetailItem::createFromArray($data);
	}

	protected function init(): void
	{
		$this->httpClient = new HttpClient();
	}
}