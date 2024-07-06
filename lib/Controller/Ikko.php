<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Controller;

use Bitrix\Ikko\Configuration\Config;
use Bitrix\Ikkomodule\Model\Menu;
use Bitrix\IkkoModule\Service\OrderService;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\HttpResponse;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class Ikko extends Controller
{
	protected HttpClient $httpClient;

	public function getDefaultPreFilters()
	{
		return [];
	}

	public function onMenuChangedAction(): HttpResponse
	{
		return new AjaxJson();
	}

	public function onItemsOrderedAction(): HttpResponse
	{
		$orders = $this->getOrders();
		return new AjaxJson();
	}

	public function onItemAppearedAction(array $addedItem = [], array $menu = []): HttpResponse
	{
		return new AjaxJson();
	}

	public function onItemExpiredAction(array $removedItem = [], array $menu = []): HttpResponse
	{
		return new AjaxJson();
	}

	public function onShiftPausedAction(): HttpResponse
	{
		$s = new OrderService();
		return new AjaxJson();
	}

	public function onShiftResumedAction(): HttpResponse
	{
		return new AjaxJson();
	}

	public function onShiftStartedAction(): HttpResponse
	{
		$menu = Menu::createFromArray($this->getMenu());
		return new AjaxJson();
	}

	public function onShiftEndedAction(): HttpResponse
	{
		return new AjaxJson();
	}

	protected function getOrders(): array
	{
		return (array)$this->getRequestData('orders');
	}

	protected function getMenu(): array
	{
		return (array)$this->getRequestData('menu');
	}

	protected function getRequestData(string $name = ''): mixed
	{
		$request = file_get_contents('php://input');
		$data = Json::decode($request);
		if ('' === $name)
		{
			return $data;
		}

		return $data[$name] ?? null;
	}

	protected function init(): void
	{
		parent::init();

		$this->httpClient = new HttpClient();
	}
}