<?php

namespace Master\Deal\UserField;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\HtmlFilter;

class MoneyRubType extends \Bitrix\Currency\UserField\Types\MoneyType
{
    public const USER_TYPE_ID = 'money_rub';

    public const RENDER_COMPONENT = 'systeme:currency.field.money.rub';

    public static function getDescription(): array
    {
        return [
            'DESCRIPTION' => Loc::getMessage('USER_TYPE_MONEY_RUB_DESCRIPTION'),
            'BASE_TYPE' => \CUserTypeManager::BASE_TYPE_STRING,
        ];
    }

    public static function checkFields(array $userField, $value): array
    {
        $fieldName = HtmlFilter::encode(
            $userField['EDIT_FORM_LABEL'] ?: $userField['FIELD_NAME']
        );

        $result = parent::checkFields($userField, $value);

        [$value, $currency] = static::unFormatFromDb($value);

        if($currency !== 'RUB'){
            $result[] = [
                'id' => $userField['FIELD_NAME'],
                'text' => Loc::getMessage('USER_TYPE_MONEY_ERR_BAD_ONLY_RUB',
                    [
                        '#FIELD_NAME#' => $fieldName,
                    ]
                ),
            ];
        }

        return $result;
    }

}