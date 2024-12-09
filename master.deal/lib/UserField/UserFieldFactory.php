<?php

namespace Master\Deal\UserField;

use CUserFieldEnum;
use CUserTypeEntity;

class UserFieldFactory
{
    const TYPE_DATE = 'date';

    const TYPE_DATETIME = 'datetime';

    const TYPE_STRING = 'string';

    const TYPE_INTEGER = 'integer';

    const TYPE_BOOLEAN = 'boolean';

    const TYPE_ENUM = 'enumeration';
    const TYPE_MONEY = 'money';
    const TYPE_MONEY_RUB = 'money_rub';
    const TYPE_CRM = 'crm';

    /**
     * Добавление пользовательского свойства
     */
    public static function add($name, $title, $title_en, $entity_id, $type, $mandatory = false, $multiple = false, $settings = [])
    {
        $oUserTypeEntity = new CUserTypeEntity();

        $aUserFields = array(
            'ENTITY_ID' => $entity_id,
            'FIELD_NAME' => 'UF_' . $name,
            'USER_TYPE_ID' => $type,
            'XML_ID' => $name,
            'SORT' => 500,
            'MULTIPLE' => ($multiple) ? 'Y' : 'N',
            'MANDATORY' => ($mandatory) ? 'Y' : 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => '',
            'EDIT_IN_LIST' => '',
            'IS_SEARCHABLE' => 'N',
            'EDIT_FORM_LABEL' => array(
                'ru' => $title,
                'en' => $title_en,
            ),
            'LIST_COLUMN_LABEL' => array(
                'ru' => $title,
                'en' => $title_en,
            ),
            'LIST_FILTER_LABEL' => array(
                'ru' => $title,
                'en' => $title_en,
            ),
            'ERROR_MESSAGE' => array(
                'ru' => 'Ошибка при заполнении пользовательского поля ' . $title,
                'en' => 'An error in completing the user field ' . $title_en,
            ),
            'HELP_MESSAGE' => array(
                'ru' => '',
                'en' => '',
            ),
            'SETTINGS' => $settings
        );

        return $oUserTypeEntity->Add($aUserFields, false);
    }

    public static function addEnum($name, $title, $title_en, $entity_id, $values, $multiple = false)
    {
        if ($id = self::add($name, $title, $title_en, $entity_id, 'enumeration', $multiple)) {
            $arAddEnum = [];
            foreach ($values as $xml => $value) {
                $i = count($arAddEnum);

                if (is_int($xml)) {
                    $xml = bin2hex(random_bytes(28));
                }

                $arAddEnum['n' . $i] = array(
                    'XML_ID' => $xml,
                    'VALUE' => $value,
                    'DEF' => 'N',
                    'SORT' => $i * 10
                );
            }

            $obEnum = new CUserFieldEnum();

            if ($obEnum->SetEnumValues($id, $arAddEnum)) {
                return $id;
            }
        }

        return false;
    }
}