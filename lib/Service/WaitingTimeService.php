<?php

namespace Bitrix\Ikkomodule\Service;

use Bitrix\Main\Type\DateTime;

class WaitingTimeService
{
	private const DEFAULT_WAITING_TIME = 60;

	private OrderService $orderService;
	private ProductComplexityService $productComplexityService;
	public function __construct()
	{
		$this->orderService = new OrderService();
		$this->productComplexityService = new ProductComplexityService();
	}

	public function calculateWaitingTime(): int
	{
		$date = (new DateTime())->add('-15 minutes');
		$orders = $this->orderService->getItemsFromTo($date);
		$complexities  = $this->productComplexityService->getAll();

		$result = 0;

		foreach ($orders as $order)
		{
			$result += $complexities[$order->getName()]?->getSecondsToMake() ?? self::DEFAULT_WAITING_TIME;
		}

		return $result;
	}
}