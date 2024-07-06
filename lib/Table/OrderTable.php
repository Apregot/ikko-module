<?php
namespace Bitrix\Ikkomodule\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

/**
 * Class OrderTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> NAME string(255) mandatory
 * <li> DATE datetime mandatory
 * </ul>
 *
 * @package Bitrix\Ikkomodule
 **/

class OrderTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_ikkomodule_order';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return [
			(new IntegerField('ID'))
				->configurePrimary(true)
				->configureAutocomplete(true)
				->configureSize(8)
			,
			(new StringField('NAME',
				[
					'validation' => function()
					{
						return[
							new LengthValidator(null, 255),
						];
					},
				]
			))
				->configureRequired(true)
			,
			(new DatetimeField('DATE'))->configureRequired(true),
		];
	}
}