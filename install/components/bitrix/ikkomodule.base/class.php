<?php

class IkkoModuleBaseComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		\Bitrix\Main\Loader::includeModule('ikkomodule');

		$a = (new \Bitrix\Ikkomodule\Service\WaitingTimeService())->calculateWaitingTime();

		$this->includeComponentTemplate();
	}
}
