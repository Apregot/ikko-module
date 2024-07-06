<?php

namespace Bitrix\Ikkomodule\Model;

class Statistic
{
	public function __construct(
		public readonly array $mostPopular,
		public readonly int $totalCount,
		public readonly int $baristaFatigue,
	)
	{
	}
}