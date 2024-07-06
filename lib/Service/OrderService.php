<?php

namespace Bitrix\IkkoModule\Service;

use Bitrix\IkkoModule\Table\OrderTable;

class OrderService
{
	public function save(Order $order): void
	{
		OrderTable::add([
			'NAME' => $order->getName(),
			'DATE' => $order->getDate(),
		]);
	}

	/** @param array<Order> $orders */
	public function saveBatch(array $orders): void
	{
		$preparedOrders = [];

		foreach ($orders as $order)
		{
			$preparedOrders[] = [
				'NAME' => $order->getName(),
				'DATE' => $order->getDate(),
			];
		}

		if (!empty($preparedOrders))
		{
			OrderTable::addMulti($preparedOrders, true);
		}
	}
}