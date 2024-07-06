<?php

class IkkoModuleBaseComponent extends CBitrixComponent
{
	public function executeComponent()
	{
		\Bitrix\Main\Loader::includeModule('ikkomodule');

		$this->includeComponentTemplate();
	}
}
