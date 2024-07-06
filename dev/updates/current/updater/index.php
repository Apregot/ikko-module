<?php
/** @var CUpdater $updater */
/** @var CDatabase $DB */

if ($updater->CanUpdateDatabase())
{
	$updater->Query('
		CREATE TABLE IF NOT EXISTS b_ikkomodule_order
		(
			ID bigint NOT NULL AUTO_INCREMENT,
			NAME varchar(255) NOT NULL,
			DATE datetime NOT NULL,
		);
	');
}
