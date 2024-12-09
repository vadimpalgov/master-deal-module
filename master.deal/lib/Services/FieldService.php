<?php

namespace Master\Deal\Services;

use Bitrix\Main\Config\Option;

class FieldService
{
    const MODULE_ID = 'master.deal';
    const DEAL_NUMBER_FIELD = 'UF_CRM_MASTER_DEAL_NUMBER';
    const DEAL_END_USER_FIELD = 'UF_CRM_MASTER_DEAL_END_USER';
    const DEAL_SUM_FIELD = 'UF_CRM_MASTER_DEAL_SUM_CURRENCY';

    /**
     * Возвращает список всех полей сделки.
     *
     * @return array Массив полей сделки.
     */
    public static function getFieldList()
    {
        return [
            self::DEAL_NUMBER_FIELD,  // Номер сделки
            self::DEAL_END_USER_FIELD,  // Конечный пользователь
            self::DEAL_SUM_FIELD  // Сумма сделки
        ];
    }

    /**
     * Возвращает имя поля для номера сделки.
     *
     * @return string Имя поля для номера сделки.
     */
    public function getDealNumberField(): string
    {
        return $this->getFieldName(self::DEAL_NUMBER_FIELD);
    }

    /**
     * Возвращает имя поля для конечного пользователя.
     *
     * @return string Имя поля для конечного пользователя.
     */
    public function getDealEndUserField(): string
    {
        return $this->getFieldName(self::DEAL_END_USER_FIELD);
    }

    /**
     * Возвращает имя поля для суммы сделки.
     *
     * @return string Имя поля для суммы сделки.
     */
    public function getSumField(): string
    {
        return $this->getFieldName(self::DEAL_SUM_FIELD);
    }

    /**
     * Получает значение поля из настроек модуля.
     *
     * @param string $fieldName Имя поля.
     * @return string Значение поля из настроек модуля или имя поля по умолчанию.
     */
    private function getFieldName(string $fieldName): string
    {
        // Получаем значение поля из настроек модуля, если оно задано
        return Option::get(self::MODULE_ID, "FIELD_{$fieldName}", $fieldName);
    }
}