<?php
/** @var CUpdater $updater */
/** @var CDatabase $DB */

if ($updater->CanUpdateDatabase())
{
	$updater->Query('
		CREATE TABLE IF NOT EXISTS b_ikkomodule_order
		(
			ID bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
			NAME varchar(255) NOT NULL,
			DATE datetime NOT NULL
		);
	');

	$updater->Query('
		CREATE TABLE IF NOT EXISTS b_ikkomodule_product_complexity (
			ID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
			NAME varchar(255) NOT NULL,
			SECONDS_TO_MAKE INT NOT NULL
		);
	');

	if ($updater->TableExists("b_ikkomodule_order"))
	{
		try {
			$updater->Query('ALTER TABLE b_ikkomodule_order ADD COLUMN ITEM_ID int not null');
		}
		catch (Exception $e)
		{}
	}
}

if ($updater->CanUpdateDatabase())
{
	if (!\Bitrix\Ikkomodule\Bot\Barista::getBotId())
	{
		\Bitrix\Ikkomodule\Bot\Barista::register();
	}
}
