<?php

namespace Bitrix\Ikkomodule\Service;

use Bitrix\Main\Type\DateTime;

class Order
{
	private int $id = 0;
	private string $name;
	private DateTime $date;

	public function __construct()
	{
		$this->date = new DateTime();
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function setId(int $id): self
	{
		$this->id = $id;

		return $this;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getDate(): DateTime
	{
		return $this->date;
	}

	public function setDate(DateTime $date): self
	{
		$this->date = $date;

		return $this;
	}
}