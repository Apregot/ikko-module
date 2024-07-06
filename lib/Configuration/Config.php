<?php

declare(strict_types=1);

namespace Bitrix\Ikkomodule\Configuration;

class Config
{
	public static function getIkkoUrl(): string
	{
		return 'http://localhost:3000';
	}
}