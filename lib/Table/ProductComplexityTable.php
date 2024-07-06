<?php
namespace Bitrix\Ikkomodule\Table;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;

/**
 * Class ProductComplexityTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> NAME string(255) mandatory
 * <li> SECONDS_TO_MAKE int mandatory
 * </ul>
 *
 * @package Bitrix\Ikkomodule
 **/

class ProductComplexityTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_ikkomodule_product_complexity';
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
			(new IntegerField('SECONDS_TO_MAKE'))
				->configureRequired(true)
			,
		];
	}
}