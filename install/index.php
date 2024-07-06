<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\FileTable;
use Bitrix\Main\IO\File;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (class_exists('Ikkomodule'))
{
	return;
}

class Ikkomodule extends \CModule
{
	public $MODULE_ID = 'ikkomodule';
	public $MODULE_GROUP_RIGHTS = 'Y';
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;

	public array $eventsData = [];

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$arModuleVersion = [];

		include(__DIR__ . '/version.php');

		if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
		{
			$this->MODULE_VERSION = $arModuleVersion['VERSION'];
			$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		}

		$this->MODULE_NAME = 'Ikko module';
		$this->MODULE_DESCRIPTION = 'Ikko module';
	}

	public function getDocumentRoot(): string
	{
		$context = \Bitrix\Main\Application::getInstance()->getContext();

		return $context->getServer()->getDocumentRoot();
	}

	/**
	 * Call all install methods.
	 * @returm void
	 */
	public function doInstall(): void
	{
		global $DB, $APPLICATION;

		$this->installFiles();
		$this->installDB();

		$GLOBALS['APPLICATION']->includeAdminFile(
			'Ikko module',
			$this->getDocumentRoot() . '/bitrix/modules/ikkomodule/install/step1.php'
		);
	}

	/**
	 * Call all uninstall methods, include several steps.
	 * @returm void
	 */
	public function doUninstall(): void
	{
		global $APPLICATION;

		$step = isset($_GET['step']) ? intval($_GET['step']) : 1;

		if ($step < 2)
		{
			$APPLICATION->includeAdminFile(
				'Ikko module',
				$this->getDocumentRoot() . '/bitrix/modules/ikkomodule/install/unstep1.php'
			);
		}
		elseif ($step === 2)
		{
			$params = [];
			if (isset($_GET['savedata']))
			{
				$params['savedata'] = $_GET['savedata'] === 'Y';
			}

			$this->uninstallDB($params);
			$this->uninstallFiles();

			$APPLICATION->includeAdminFile(
				'Ikko module',
				$this->getDocumentRoot() . '/bitrix/modules/ikkomodule/install/unstep2.php'
			);
		}
	}

	/**
	 * Install DB, events, etc.
	 * @return bool
	 */
	public function installDB(): bool
	{
		global $DB, $APPLICATION;

		// db
		if (File::isFileExists($this->getDocumentRoot() . "/bitrix/modules/ikkomodule/install/db/{$this->getConnectionType()}/install.sql"))
		{
			$errors = $DB->runSQLBatch(
				$this->getDocumentRoot() . "/bitrix/modules/ikkomodule/install/db/{$this->getConnectionType()}/install.sql"
			);
			if ($errors !== false)
			{
				$APPLICATION->throwException(implode('', $errors));
				return false;
			}
		}

		// module
		registerModule($this->MODULE_ID);

		// install event handlers
		$eventManager = EventManager::getInstance();
		foreach ($this->eventsData as $module => $events)
		{
			foreach ($events as $eventCode => $callback)
			{
				$eventManager->registerEventHandler(
					$module,
					$eventCode,
					$this->MODULE_ID,
					$callback[0],
					$callback[1]
				);
			}
		}

		return true;
	}

	/**
	 * Install files.
	 * @return bool
	 */
	public function installFiles(): bool
	{
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ikkomodule/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ikkomodule/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js", true, true);

		return true;
	}

	/**
	 * Uninstall DB, events, etc.
	 * @param array $arParams Some params.
	 * @return bool
	 */
	public function uninstallDB(array $arParams = []): bool
	{
		global $APPLICATION, $DB;

		$errors = false;

		// delete DB
		if (File::isFileExists($this->getDocumentRoot() . "/bitrix/modules/ikkomodule/install/db/{$this->getConnectionType()}/uninstall.sql"))
		{
			if (isset($arParams['savedata']) && !$arParams['savedata'])
			{
				$errors = $DB->runSQLBatch(
					$this->getDocumentRoot() . "/bitrix/modules/ikkomodule/install/db/{$this->getConnectionType()}/uninstall.sql"
				);
			}
		}

		if ($errors !== false)
		{
			$APPLICATION->throwException(implode('', $errors));
			return false;
		}

		// agents and rights
		CAgent::removeModuleAgents($this->MODULE_ID);
		$this->unInstallTasks();

		// uninstall event handlers
		$eventManager = EventManager::getInstance();
		foreach ($this->eventsData as $module => $events)
		{
			foreach ($events as $eventCode => $callback)
			{
				$eventManager->unregisterEventHandler(
					$module,
					$eventCode,
					$this->MODULE_ID,
					$callback[0],
					$callback[1]
				);
			}
		}

		// module
		unregisterModule($this->MODULE_ID);

		// delete files finally
		if (isset($arParams['savedata']) && !$arParams['savedata'])
		{
			$res = FileTable::getList([
				'select' => [
					'ID',
				],
				'filter' => [
					'=MODULE_ID' => $this->MODULE_ID,
				],
				'order' => [
					'ID' => 'desc',
				],
			]);
			while ($row = $res->fetch())
			{
				CFile::delete($row['ID']);
			}
		}

		return true;
	}

	/**
	 * Uninstall files.
	 * @return bool
	 */
	public function uninstallFiles(): bool
	{
		DeleteDirFilesEx("/bitrix/js/ikkomodule/");

		return true;
	}

	private function getConnectionType(): string
	{
		return \Bitrix\Main\Application::getConnection()->getType();
	}
}
