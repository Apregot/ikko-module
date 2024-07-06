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
}