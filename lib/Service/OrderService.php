<?php

namespace Bitrix\Ikkomodule\Service;

use Bitrix\IkkoModule\Table\OrderTable;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Type\DateTime;

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

	public function getCountFromTo(DateTime $from, DateTime $to = new DateTime()): int
	{
		$result = OrderTable::query()
			->addSelect('ORDER_COUNT')
			->where('DATE', '>=', $from)
			->where('DATE', '<=', $to)
			->registerRuntimeField('', new ExpressionField('ORDER_COUNT', 'COUNT(%s)', 'ID'))
			->fetch()
		;

		return (int)$result['ORDER_COUNT'];
	}
}