<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Model;

class DetailItem
{
	public static function createFromArray(array $data): static
	{
		$id = (int)($data['id'] ?? 0);
		$description = (string)($data['description'] ?? '');
		$imageUrl = (string)($data['imageUrl'] ?? '');
		$title = (string)($data['title'] ?? '');
		$categoryId = (int)($data['categoryId'] ?? 0);
		$category = Category::createFromArray($data['category'] ?? []);
		$isAvailable = (bool)($data['available'] ?? false);

		$modifiers = $data['modifiers'] ?? [];
		$dtoModifiers = [];
		foreach ($modifiers as $modifier)
		{
			$dtoModifier = Modifier::createFromArray($modifier);
			$dtoModifiers[$dtoModifier->id] = $dtoModifier;
		}

		return new static($id, $description, $imageUrl, $title, $categoryId, $category, $isAvailable, $dtoModifiers);
	}

	public function __construct(
		public readonly int $id,
		public readonly string $description,
		public readonly string $imageUrl,
		public readonly string $title,
		public readonly int $categoryId,
		public readonly Category $category,
		public readonly bool $available,
		/** @var Modifier[] */
		public readonly array $modifiers
	)
	{

	}
}