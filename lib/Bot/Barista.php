<?php

namespace Bitrix\Ikkomodule\Bot;

use Bitrix\ImBot\Bot\Base;
use Bitrix\Main\Loader;

Loader::requireModule('imbot');
Loader::requireModule('im');

class Barista extends Base
{
	public const BOT_CODE = 'barista';
	public const MODULE_ID = 'ikkomodule';

	public static function getOrCreateId(): int
	{
		if (!self::getBotId())
		{
			self::register();
		}

		return self::getBotId();
	}

	public static function register(array $params = [])
	{
		$birthday = new \Bitrix\Main\Type\DateTime('2024-10-01 19:45:00', 'Y-m-d H:i:s');
		$birthday = $birthday->format(\Bitrix\Main\Type\Date::convertFormatToPhp(\CSite::GetDateFormat('SHORT')));
		$avatarUrl = \Bitrix\Main\Application::getDocumentRoot().'/bitrix/modules/ikkomodule/install/avatar/barista/default.png';
		$avatar = \CFile::makeFileArray($avatarUrl);

		$botId = \Bitrix\Im\Bot::register([
			'CODE' => self::BOT_CODE,
			'TYPE' => \Bitrix\Im\Bot::TYPE_BOT,
			'MODULE_ID' => self::MODULE_ID,
			'CLASS' => __CLASS__,
			'LANG' => 'ru',
			'INSTALL_TYPE' => \Bitrix\Im\Bot::INSTALL_TYPE_SILENT,
			'METHOD_MESSAGE_ADD' => 'onMessageAdd',
			'METHOD_WELCOME_MESSAGE' => 'onChatStart',
			'METHOD_BOT_DELETE' => 'onBotDelete',
			'PROPERTIES' => [
				'NAME' => 'Бариста',
				'COLOR' => 'PINK',
				'PERSONAL_BIRTHDAY' => $birthday,
				'WORK_POSITION' => 'Бариста',
				'PERSONAL_GENDER' => 'M',
				'PERSONAL_PHOTO' => $avatar,
			]
		]);

		if ($botId)
		{
			self::setBotId($botId);
		}
	}

	public static function unRegister()
	{
		Loader::requireModule('im');
		$result = \Bitrix\Im\Bot::unRegister(['BOT_ID' => self::getBotId()]);
		if ($result)
		{
			self::setBotId(0);
		}
	}
}