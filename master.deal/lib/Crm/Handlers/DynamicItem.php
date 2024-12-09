<?php

namespace Master\Deal\Crm\Handlers;

use Bitrix\Crm\Item;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Event;
use Bitrix\Main\Loader;

class DynamicItem
{
    public static function OnCrmDynamicItemAdd(Event $event)
    {
        Loader::includeModule('master.deal');

        /** @var \Master\Deal\Services\FieldService $fieldService */
        $fieldService = ServiceLocator::getInstance()->get('master.deal.field');

        /** @var \Master\Deal\Services\TypeService $typeService */
        $typeService = ServiceLocator::getInstance()->get('master.deal.types');

        if($typeService){

            /** @var Item $item */
            $item = $event->getParameter('item');

            if($item->getEntityTypeId() === $typeService->getMasterDealTypeId()){

                if($newName = self::createMasterDealName($item)){
                    $item->set($fieldService->getDealNumberField(), $newName);
                }

            }

            if($item->isChanged($fieldService->getDealNumberField())){
                $item->save();
            }
        }
    }

    public static function OnCrmDynamicItemUpdate(Event $event)
    {
        /** @var \Master\Deal\Services\FieldService $fieldService */
        $fieldService = ServiceLocator::getInstance()->get('master.deal.field');

        /** @var \Master\Deal\Services\TypeService $typeService */
        $typeService = ServiceLocator::getInstance()->get('master.deal.types');

        if($typeService){

            /** @var Item\Dynamic $item */
            $item = $event->getParameter('item');

            if($item->getEntityTypeId() === $typeService->getMasterDealTypeId()){

                if($sum = self::calculateDealSum($item)){
                    $item->set($fieldService->getSumField(), $sum);
                }

            }

            if($item->isChanged($fieldService->getSumField())){
                $item->save();
            }
        }
    }

    private static function createMasterDealName(Item $item)
    {
        $template = Option::get('master.deal', 'MASTER_DEAL_NAME_TEMPLATE', 'МСД-{{Когда создан}}-{{ID}}');

        $date = date("d.m.Y");

        /** @var \Master\Deal\Services\NameService $nameService */
        $nameService = ServiceLocator::getInstance()->get('master.deal.name');

        if($newName = $nameService->generateNameFromTemplate($template, $date, $item->getId())){
            return $newName;
        }

        return null;
    }

    private static function calculateDealSum(Item\Dynamic $item)
    {
        /** @var \Master\Deal\Services\SumService $sumService */
        $sumService = ServiceLocator::getInstance()->get('master.deal.sum');

        $sumService->calculateAndUpdate($item);
    }
}