<?php

namespace Bitrix\Ikkomodule\Model;

use Bitrix\Main\Type\DateTime;

class Order
{
	private int $itemId;
	private string $name;
	private DateTime $date;

	public function __construct()
	{
		$this->date = new DateTime();
	}

	public function getItemId(): int
	{
		return $this->itemId;
	}

	public function setDate(DateTime $date): static
	{
		$this->date = $date;

		return $this;
	}

	public function setItemId(int $itemId): self
	{
		$this->itemId = $itemId;

		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): Order
	{
		$this->name = $name;

		return $this;
	}

	public function getDate(): DateTime
	{
		return $this->date;
	}
}