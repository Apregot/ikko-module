<?php

namespace Bitrix\Ikkomodule\Service;

use Bitrix\Ikkomodule\Model\ProductComplexity;
use Bitrix\Ikkomodule\Table\ProductComplexityTable;
use Bitrix\Main\Type\DateTime;

class ProductComplexityService
{
	/** @return array<ProductComplexity> */
	public function getAll(): array
	{
		$complexities = ProductComplexityTable::query()
			->setSelect(['*'])
			->fetchAll()
		;

		$result = [];

		foreach ($complexities as $complexity)
		{
			$productComplexity =
				(new ProductComplexity())
					->setId($complexity['ID'])
					->setName($complexity['NAME'])
					->setSecondsToMake($complexity['SECONDS_TO_MAKE'])
			;

			$result[$complexity['NAME']] = $productComplexity;
		}

		return $result;
	}
}