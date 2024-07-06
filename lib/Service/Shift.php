<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Service;

use Bitrix\Main\Config\Option;

class Shift
{
	public function isOpened(): bool
	{
		return (int)Option::get('ikkomodule', 'barista_is_here', 0) === 1;
	}

	public function start(): void
	{
		Option::set('ikkomodule', 'barista_is_here', 1);
	}

	public function finish(): void
	{
		Option::set('ikkomodule', 'barista_is_here', 0);
	}

	public function resume(): void
	{
		Option::set('ikkomodule', 'barista_is_here', 1);
	}

	public function pause(): void
	{
		Option::set('ikkomodule', 'barista_is_here', 0);
	}
}