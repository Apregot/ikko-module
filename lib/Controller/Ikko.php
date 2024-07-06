<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Controller;

use Bitrix\Ikko\Configuration\Config;
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

	public function onMenuChangedAction(array $menu = []): HttpResponse
	{
		$response = $this->httpClient->post(Config::getIkkoUrl());
		$data = Json::decode($response);
		if ($data['status'] === 'success')
		{
			$menu = $data['menu'];
		}

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
		return new AjaxJson();
	}

	public function onShiftResumedAction(): HttpResponse
	{
		return new AjaxJson();
	}

	public function onShiftStartedAction(array $menu = []): HttpResponse
	{
		return new AjaxJson();
	}

	public function onShiftEndedAction(): HttpResponse
	{
		return new AjaxJson();
	}

	protected function init(): void
	{
		parent::init();

		$this->httpClient = new HttpClient();
	}
}