<?php

namespace Bitrix\Ikkomodule\Model;

class Status
{
	public function __construct(
		public readonly Menu $menu,
		public readonly int $waitingTime,
		public readonly bool $idle,
	)
	{
	}
}