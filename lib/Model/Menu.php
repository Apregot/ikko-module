<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Model;

class Menu
{
	public static function createFromArray(array $data)
	{
		$items = $data['items'] ?? [];
		$dtoItems = [];
		foreach ($items as $item)
		{
			$dtoItem  =  MenuItem::createFromArray($item);
			$dtoItems[$dtoItem->id] = $dtoItem;
		}

		$categories = $data['categories'] ?? [];
		$dtoCategories = [];
		foreach ($categories as $category)
		{
			$dtoCategory = Category::createFromArray($category);
			$dtoCategories[$dtoCategory->id] = $dtoCategory;
		}

		$modifiers = $data['modifiers'] ?? [];
		$dtoModifiers = [];
		foreach ($modifiers as $modifier)
		{
			$dtoModifier = Modifier::createFromArray($modifier);
			$dtoModifiers[$dtoModifier->id] = $dtoModifier;
		}

		return new static($dtoItems, $dtoCategories, $dtoModifiers);
	}
	public function __construct(
		/**  @var MenuItem[]  */
		public readonly array $items,
		/** @var Category[] */
		public readonly array $categories,
		/**  @var Modifier[]  */
		public readonly array $modifiers
	)
	{
		$this->groupItems();
	}

	public function groupItems(): void
	{
		$items = [];
		foreach ($this->items as $item)
		{
			$items[$item->categoryId][] = $item;
		}


		foreach ($items as $categoryId => $menuItems)
		{
			$this->categories[$categoryId]->setItems($menuItems);
		}
	}
}