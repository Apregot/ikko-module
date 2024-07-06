<?php

namespace Bitrix\Ikkomodule\Service;

use Bitrix\Ikkomodule\Model\Order;
use Bitrix\IkkoModule\Table\OrderTable;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\Type\DateTime;

class OrderService
{
	public function save(Order $order): void
	{
		OrderTable::add([
			'NAME' => $order->getName(),
			'ITEM_ID' => $order->getItemId(),
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
				'ITEM_ID' => $order->getItemId(),
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

	/** @return  array<Order> */
	public function getItemsFromTo(DateTime $from, DateTime $to = new DateTime()): array
	{
		$items = OrderTable::query()
			->setSelect(['*'])
			->where('DATE', '>=', $from)
			->where('DATE', '<=', $to)
			->fetchAll()
		;

		$result = [];

		foreach ($items as $item)
		{
			$result[] = (new Order())
				->setId($item['ID'])
				->setName($item['NAME'])
				->setItemId($item['ITEM_ID'])
				->setDate($item['DATE'])
			;
		}

		return $result;
	}
}