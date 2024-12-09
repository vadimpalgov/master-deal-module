<?php

namespace Master\Deal\UserField;

use CUserFieldEnum;
use CUserTypeEntity;

class UserFieldEnum
{

    /**
     * Добавление пользовательского свойства
     */
    public static function add($name, $title, $entity_id, $values)
    {

        $oUserTypeEntity = new CUserTypeEntity();

        $aUserFields = array(
            'ENTITY_ID' => $name,
            'FIELD_NAME' => 'UF_' . $name,
            'USER_TYPE_ID' => 'enumeration',
            'XML_ID' => $name,
            'SORT' => 500,
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => '',
            'EDIT_IN_LIST' => '',
            'IS_SEARCHABLE' => 'N',
            'EDIT_FORM_LABEL' => array(
                'ru' => $title,
                'en' => 'User field',
            ),
            'LIST_COLUMN_LABEL' => array(
                'ru' => $title,
                'en' => 'User field',
            ),
            'LIST_FILTER_LABEL' => array(
                'ru' => $title,
                'en' => 'User field',
            ),
            'ERROR_MESSAGE' => array(
                'ru' => 'Ошибка при заполнении поля ' . $title,
                'en' => 'An error in completing the user field',
            ),
            'HELP_MESSAGE' => array(
                'ru' => '',
                'en' => '',
            ),
        );

        $userFieldId = $oUserTypeEntity->Add($aUserFields);

        if (!$userFieldId) {
            return false;
        }

        $arAddEnum = [];
        foreach ($values as $xml => $value) {
            $i = count($arAddEnum);
            $arAddEnum['n' . $i] = array(
                'XML_ID' => $xml,
                'VALUE' => $value,
                'DEF' => 'N',
                'SORT' => $i * 10
            );
        }

        $obEnum = new CUserFieldEnum();
        $result = $obEnum->SetEnumValues($userFieldId, $arAddEnum);

        if (!$result) {
            return false;
        }
    }

    public static function remove($name)
    {

    }

}