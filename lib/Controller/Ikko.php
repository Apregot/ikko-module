<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Controller;

use Bitrix\Ikkomodule\Configuration\Config;
use Bitrix\Ikkomodule\Bot\Barista;
use Bitrix\Ikkomodule\Chat;
use Bitrix\Ikkomodule\Model\Menu;
use Bitrix\Ikkomodule\Model\Status;
use Bitrix\Ikkomodule\Service\MenuService;
use Bitrix\IkkoModule\Service\OrderService;
use Bitrix\Ikkomodule\Service\Shift;
use Bitrix\Ikkomodule\Service\WaitingTimeService;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\HttpResponse;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;

class Ikko extends Controller
{
	protected HttpClient $httpClient;
	protected Shift $shift;

	public function getDefaultPreFilters()
	{
		return [];
	}

	public function onMenuChangedAction(): HttpResponse
	{
		$this->updateStatus();
		return new AjaxJson();
	}

	public function onItemsOrderedAction(): HttpResponse
	{
		$orders = $this->getOrders();
		// save
		return new AjaxJson();
	}

	public function onItemAppearedAction(): HttpResponse
	{
		$menu = Menu::createFromArray($this->getMenu());
		$this->updateStatus($menu);
		//send

		return new AjaxJson();
	}

	public function onItemExpiredAction(): HttpResponse
	{
		$menu = Menu::createFromArray($this->getMenu());
		$this->updateStatus($menu);
		// send message

		return new AjaxJson();
	}

	public function onShiftPausedAction(): HttpResponse
	{
		$this->shift->pause();
		$this->updateStatus();
		// send message

		return new AjaxJson();
	}

	public function onShiftResumedAction(): HttpResponse
	{
		$this->shift->resume();
		$this->updateStatus();
		//send message

		return new AjaxJson();
	}

	public function onShiftStartedAction(): HttpResponse
	{
		$this->shift->start();
		$menu = Menu::createFromArray($this->getMenu());
		$this->updateStatus($menu);
		//send message

		return new AjaxJson();
	}

	public function onShiftEndedAction(): HttpResponse
	{
		$this->shift->finish();
		$this->updateStatus();
		//send message
		//send stat

		return new AjaxJson();
	}

	public function onDetailInfoClickAction(int $productId): int
	{
		return Barista::getBotId();
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

	protected function updateStatus(?Menu $menu = null): void
	{
		$newMenu = $menu ?? (new MenuService())->fetch();
		$newWaitingTime = (new WaitingTimeService())->calculateWaitingTime();
		$newIdle = !(new Shift())->isOpened();
		Chat::get()->updateStatus(new Status($newMenu, $newWaitingTime, $newIdle));
	}

	protected function init(): void
	{
		parent::init();

		$this->httpClient = new HttpClient();
		$this->shift = new Shift();
	}
}