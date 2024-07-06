<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Model;

class Modifier
{
	public static function createFromArray(array $data)
	{
		$id = (int)($data['id'] ?? 0);
		$title = (string)($data['title'] ?? '');
		$type = (string)($data['type'] ?? '');
		$isAvailable = (bool)($data['available'] ?? false);

		return new static($id, $title, $type, $isAvailable);
	}

	public function __construct(
		public readonly int    $id,
		public readonly string $title,
		public readonly string $type,
		public readonly bool   $isAvailable,
	)
	{
	}
}