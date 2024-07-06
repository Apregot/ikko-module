<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Model;

class MenuItem
{
	public static function createFromArray(array $data)
	{
		$id = (int)($data['id'] ?? 0);
		$title = (string)($data['title'] ?? '');
		$isAvailable = (bool)($data['available'] ?? false);
		$categoryId = (int)($data['categoryId'] ?? 0);

		return new static($id, $title, $isAvailable, $categoryId);
	}

	public function __construct(
		public readonly int    $id,
		public readonly string $title,
		public readonly bool   $isAvailable,
		public readonly int    $categoryId,
	)
	{
	}
}