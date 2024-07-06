<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Model;

class Category
{
	public static function createFromArray(array $data)
	{
		$id = (int)($data['id'] ?? 0);
		$title = (string)($data['title'] ?? '');

		return new static($id, $title);
	}

	public function __construct(
		public readonly int    $id,
		public readonly string $title,
		public array $items = []
	)
	{
	}

	public function setItems(array $items): static
	{
		$this->items = $items;

		return $this;
	}
}