<?php

namespace Bitrix\Ikkomodule\Service;

use Bitrix\Ikkomodule\Model\Order;
use Bitrix\Ikkomodule\Model\Statistic;
use Bitrix\IkkoModule\Table\OrderTable;
use Bitrix\Main\Entity\ExpressionField;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Type\DateTime;

class OrderService
{
	public function getTotalCount(): int
	{
		$todayA = (new DateTime())->setTime(0, 0, 0);
		$todayB  =  (new DateTime())->setTime(23, 59, 59);

		$row = OrderTable::query()
			->setSelect([Query::expr('TOTAL')->count('ID')])
			->where('DATE', '>=', $todayA)
			->where('DATE', '<=', $todayB)
			->exec()
			->fetch();

		return (int)($row['TOTAL'] ?? 0);
	}

	public function getMostPopular(): array
	{
		$todayA = (new DateTime())->setTime(0, 0, 0);
		$todayB  =  (new DateTime())->setTime(23, 59, 59);

		$rows = OrderTable::query()
			->setSelect(['NAME'])
			->where('DATE', '>=', $todayA)
			->where('DATE', '<=', $todayB)
			->exec()
			->fetchAll();

		$names = array_column($rows, 'NAME');
		$res = array_fill_keys($names, 0);
		foreach ($rows as $row)
		{
			$res[$row['NAME']]++;
		}

		$res = array_filter($res);
		arsort($res);
		if (count($res) > 3)
		{
			$l = count($res);
			$res = array_slice($res, 0, 3);
		}

		return $res;
	}

	public function getBaristaFatigue(): int
	{
		$total = $this->getTotalCount();

		return (int)($total * 1.16);
	}

	public function getStatistic(): Statistic
	{
		return new Statistic(
			$this->getMostPopular(),
			$this->getTotalCount(),
			$this->getBaristaFatigue(),
		);
	}

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
				->setName($item['NAME'])
				->setItemId($item['ITEM_ID'])
				->setDate($item['DATE'])
			;
		}

		return $result;
	}
}