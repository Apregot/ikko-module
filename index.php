<?php

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php';

$APPLICATION->IncludeComponent(
	'bitrix:ikkomodule.base',
	'',
	[
		'SEF_FOLDER' => '/ikkomodule/',
	]
);

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php';