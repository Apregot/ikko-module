<?php

namespace Bitrix\Ikkomodule;

use Bitrix\Ikkomodule\Bot\Barista;
use Bitrix\Im\V2\Chat\ChatFactory;
use Bitrix\Im\V2\Chat\OpenChannelChat;
use Bitrix\Im\V2\Link\Pin\PinService;
use Bitrix\Im\V2\Message;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;

Loader::requireModule('im');

class Chat
{
	private static self $instance;
	private const CHAT_ID_OPTION_NAME = 'ikko_chat_id';
	private const TODAY_STATUS_MESSAGE_ID_OPTION_NAME = 'ikko_status_message_id';

	private function __construct()
	{
	}

	public static function get()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new static();
		}

		return self::$instance;
	}

	public function sendSimple(string $message): void
	{
		$this->sendInternal(['MESSAGE' => $message]);
	}

	public function sendStatus(): int
	{
		$id = $this->sendInternal(['MESSAGE' => 'menu']);
		$this->setStatusMessageId($id);
		$message = new Message($id);
		(new PinService())->setContextUser(Barista::getOrCreateId())->pinMessage($message);

		return $id;
	}

	public function updateStatus(): int
	{
		$messageId = $this->getStatusMessageId();

		if (!$messageId)
		{
			return $this->sendStatus();
		}

		$message = new Message($messageId);

		if ($this->isMessageFromAnotherDay($message))
		{
			return $this->sendStatus();
		}

		(new Message\Update\UpdateService($message))
			->setContextUser(Barista::getOrCreateId())
			->update(['MESSAGE' => 'menu1'])
		;

		return $messageId;
	}

	private function isMessageFromAnotherDay(Message $message): bool
	{
		$dayOfMessage = (int)$message->getDateCreate()->format('d');
		$nowDay = (int)(new DateTime())->format('d');

		return $dayOfMessage !== $nowDay;
	}

	private function setStatusMessageId(int $id): void
	{
		Option::set('ikkomodule', self::TODAY_STATUS_MESSAGE_ID_OPTION_NAME, $id);
	}

	private function getStatusMessageId(): int
	{
		return (int)Option::get('ikkomodule', self::TODAY_STATUS_MESSAGE_ID_OPTION_NAME, 0);
	}

	private function sendInternal(array $messageFields): int
	{
		$baseFields = [
			'FROM_USER_ID' => Barista::getOrCreateId(),
			'TO_CHAT_ID' => $this->getChat()->getId(),
			'MESSAGE_TYPE' => $this->getChat()->getType(),
		];
		$fields = array_merge($messageFields, $baseFields);

		return \CIMMessenger::Add($fields);
	}

	private function getChat(): OpenChannelChat
	{
		$chatId = $this->getChatId();

		if (!$chatId)
		{
			$chatId = $this->createChat();
		}

		return OpenChannelChat::getInstance($chatId);
	}

	private function getChatId(): int
	{
		return (int)Option::get('ikkomodule', self::CHAT_ID_OPTION_NAME, 0);
	}

	private function createChat(): int
	{
		$result = ChatFactory::getInstance()->addChat([
			'TYPE' => \Bitrix\Im\V2\Chat::IM_TYPE_OPEN_CHANNEL,
			'AUTHOR_ID' => Barista::getOrCreateId(),
			'TITLE' => 'Кофейня',
			'AVATAR' => $this->getAvatarId(),
			'USERS' => [Barista::getOrCreateId()]
		]);

		if (!$result->isSuccess())
		{
			return 0;
		}

		$chatId = $result->getResult()['CHAT_ID'] ?? 0;

		if ($chatId)
		{
			Option::set('ikkomodule', self::CHAT_ID_OPTION_NAME, $chatId);
		}

		return $chatId;
	}

	private function getAvatarId(): int
	{
		$avatarUrl = \Bitrix\Main\Application::getDocumentRoot().'/bitrix/modules/ikkomodule/install/avatar/channel/default.jpeg';
		$avatar = \CFile::makeFileArray($avatarUrl);

		return \CFile::SaveFile($avatar, 'ikkomodule');
	}
}