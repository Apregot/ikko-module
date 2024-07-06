<?php

namespace Bitrix\Ikkomodule;

use Bitrix\Ikkomodule\Bot\Barista;
use Bitrix\Ikkomodule\Model\DetailItem;
use Bitrix\Ikkomodule\Model\MenuItem;
use Bitrix\Ikkomodule\Model\Modifier;
use Bitrix\Ikkomodule\Model\Statistic;
use Bitrix\Ikkomodule\Model\Status;
use Bitrix\Ikkomodule\Service\DetailService;
use Bitrix\Ikkomodule\Service\OrderService;
use Bitrix\Im\V2\Chat\ChatFactory;
use Bitrix\Im\V2\Chat\OpenChannelChat;
use Bitrix\Im\V2\Link\Pin\PinService;
use Bitrix\Im\V2\Message;
use Bitrix\Main\Application;
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

	public function sendDetail(int $itemId, int $userId): void
	{
		$detail = (new DetailService())->fetch($itemId);
		$this->sendToUser($userId, ['MESSAGE' => $this->getDetailText($detail)]);
	}

	public function sendToUser(int $userId, array $messageFields): int
	{
		$baseFields = [
			'FROM_USER_ID' => Barista::getOrCreateId(),
			'DIALOG_ID' => $userId,
			'MESSAGE_TYPE' => \Bitrix\Im\V2\Chat::IM_TYPE_PRIVATE,
		];
		$fields = array_merge($messageFields, $baseFields);

		return \CIMMessenger::Add($fields);
	}

	private function getDetailText(DetailItem $detailItem): string
	{
		$text = "Расскажу подробнее о выбранном продукте\n";
		$text .= $detailItem->description . "\n";
		$text .= $detailItem->imageUrl;

		return $text;
	}

	public function sendItemAppeared(MenuItem $item): void
	{
		$this->sendSimple("Появился {$item->title} :)");
	}

	public function sendItemExpired(MenuItem $item): void
	{
		$this->sendSimple("Закончился {$item->title} :(");
	}

	public function sendShiftPaused(): void
	{
		$this->sendSimple("Бариста отошел и скоро вернется");
	}

	public function sendShiftResumed(): void
	{
		$this->sendSimple("Бариста вернулся и готов принимать заказы");
	}

	public function sendShiftStarted(): void
	{
		$this->sendSimple("Кофейня открылась!");
	}

	public function sendShiftEnded(): void
	{
		$statistic = $this->getStatisticText();
		$this->sendSimple("Кофейня на сегодня закончила свою работу.\n{$statistic}\nДо завтра!");
	}

	private function getStatisticText(): string
	{
		$statistic = (new OrderService())->getStatistic();
		$text = "Статистика за день:\n";
		$text .= "Самые популярные напитки:\n";
		$text .= $this->getMostPopularText($statistic) . "\n";
		$text .= "Всего заказов: {$statistic->totalCount}! Вы вообще работаете?\n";
		$text .= "Усталость баристы: {$statistic->baristaFatigue}%";

		return $text;
	}

	private function getMostPopularText(Statistic $statistic): string
	{
		$text = '';
		$index = 1;

		foreach ($statistic->mostPopular as $name => $count)
		{
			$text .= "{$index}. {$name} ({$count})\n";
			$index++;
		}

		return $text;
	}

	public function sendSimple(string $message): void
	{
		$this->sendInternal(['MESSAGE' => $message]);
	}

	public function sendStatus(Status $status): int
	{
		$id = $this->sendInternal($this->getMessageFieldsByStatus($status));
		$this->setStatusMessageId($id);
		$message = new Message($id);
		(new PinService())->setContextUser(Barista::getOrCreateId())->pinMessage($message);

		return $id;
	}

	public function updateStatus(Status $status): int
	{
		$messageId = $this->getStatusMessageId();

		if (!$messageId)
		{
			return $this->sendStatus($status);
		}

		$message = new Message($messageId);

		if ($this->isMessageFromAnotherDay($message))
		{
			return $this->sendStatus($status);
		}

		(new Message\Update\UpdateService($message))
			->setContextUser(Barista::getOrCreateId())
			->update($this->getMessageFieldsByStatus($status))
		;

		return $messageId;
	}

	private function getMessageFieldsByStatus(Status $status): array
	{
		$message = $this->getMessageByStatus($status);
		$attach = $this->getAttachByStatus($status);

		return ['MESSAGE' => $message, 'ATTACH' => $attach];
	}

	private function getMessageByStatus(Status $status): string
	{
		$lastDateUpdate = (new DateTime())->toUserTime();
		$waitingTime = (int)($status->waitingTime / 60) . ' мин.';
		$idle = $status->idle ? 'нет' : 'да';

		return "Последнее обновление: {$lastDateUpdate}\nПримерное время ожидания: {$waitingTime}\nНа месте ли бариста: {$idle}";
	}

	private function getAttachByStatus(Status $status): \CIMMessageParamAttach
	{
		$attach = new \CIMMessageParamAttach();
		$aboutLink = Application::getDocumentRoot() . '/ikko/product-info/';
		foreach ($status->menu->categories as $category)
		{
			$attach->AddMessage($category->title);
			$grid = [];
			$index = 1;
			foreach ($category->items as $item)
			{
				$grid[] = [
					'VALUE' => "{$index}. {$item->title}",
					'LINK' => $aboutLink . $item->id,
					'DISPLAY' => 'LINE',
					'WIDTH' => 200,
				];
				$index++;
			}
			$attach->AddGrid($grid);
			$attach->AddDelimiter();
		}
		$attach->AddMessage('Молоко');
		$attach->AddGrid($this->getGridForMilk($status->menu->modifiers));

		return $attach;
	}

	/**
	 * @param Modifier[] $modifiers
	 * @return array
	 */
	private function getGridForMilk(array $modifiers): array
	{
		$grid = [];
		$index = 1;

		foreach ($modifiers as $modifier)
		{
			if ($modifier->type !== 'milk')
			{
				continue;
			}

			$grid[] = [
				'VALUE' => "{$index}. {$modifier->title}",
				'DISPLAY' => 'LINE',
				'WIDTH' => 200,
			];

			$index++;
		}

		return $grid;
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