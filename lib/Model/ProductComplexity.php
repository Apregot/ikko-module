<?php

namespace Bitrix\Ikkomodule\Model;

class ProductComplexity
{
	private int $id = 0;
	private string $name;
	private int $secondsToMake;

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

	public function getSecondsToMake(): int
	{
		return $this->secondsToMake;
	}

	public function setSecondsToMake(int $secondsToMake): self
	{
		$this->secondsToMake = $secondsToMake;

		return $this;
	}
}