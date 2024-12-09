<?php

namespace Master\Deal\Services;

use Bitrix\Main\Loader;
use Bitrix\Crm\Model\Dynamic\TypeTable;

Loader::includeModule('crm');

class TypeService
{
    public function getMasterDealTypeId()
    {
        return (int)$this->getEntityTypeIdByName('MASTER_DEAL');
    }

    private function getEntityTypeIdByName(string $code)
    {
        $entity = TypeTable::getList([
            'filter' => ['CODE' => $code],
            'select' => ['ENTITY_TYPE_ID']
        ])->fetch();

        return $entity ? $entity['ENTITY_TYPE_ID'] : null;
    }
}