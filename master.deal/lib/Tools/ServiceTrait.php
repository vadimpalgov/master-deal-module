<?php

namespace Master\Deal\Tools;

use Bitrix\Main\DI\ServiceLocator;
use Master\Deal\Services\DealService;
use Master\Deal\Services\FieldService;
use Master\Deal\Services\NameService;
use Master\Deal\Services\TypeService;

trait ServiceTrait
{
    public function getDealService(): DealService
    {
        return ServiceLocator::getInstance()->get('master.deal');
    }

    public function getTypeService(): TypeService
    {
        return ServiceLocator::getInstance()->get('master.deal.types');
    }

    public function getFieldService(): FieldService
    {
        return ServiceLocator::getInstance()->get('master.deal.field');
    }

    public function getNameService(): NameService
    {
        return ServiceLocator::getInstance()->get('master.deal.name');
    }
    public function getSumService(): NameService
    {
        return ServiceLocator::getInstance()->get('master.deal.sum');
    }
}