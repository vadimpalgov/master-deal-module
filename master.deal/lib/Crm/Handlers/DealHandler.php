<?php

namespace Master\Deal\Crm\Handlers;

use Bitrix\Crm\Service\Container;
use Bitrix\Main\DI\ServiceLocator;
use Bitrix\Main\Type\DateTime;
use Master\Deal\Tools\ServiceTrait;


class DealHandler
{
    use ServiceTrait;

    public static function OnAfterCrmDealAdd($fields)
    {
        /** @var \Master\Deal\Services\TypeService $typeService */
        $typeService = ServiceLocator::getInstance()->get('master.deal.types');
        $masterDealTypeId = $typeService->getMasterDealTypeId();
        $masterDealParentFieldName = "PARENT_ID_{$masterDealTypeId}";
        $masterDealId = $fields[$masterDealParentFieldName] ?? null;

        if($fields['OPPORTUNITY'] && $masterDealId) {

            /** @var \Master\Deal\Services\DealService $dealService */
            $dealService = ServiceLocator::getInstance()->get('master.deal');

            if($masterDeal = $dealService->getMasterDealById($fields[$masterDealParentFieldName])) {
                self::addCalculateAgent($masterDeal->getId());
            }
        }
    }

    public static function OnBeforeCrmDealUpdate($fields)
    {
        /** @var \Master\Deal\Services\TypeService $typeService */
        $typeService = ServiceLocator::getInstance()->get('master.deal.types');

        $masterDealTypeId = $typeService->getMasterDealTypeId();
        $masterDealParentFieldName = "PARENT_ID_{$masterDealTypeId}";
        $masterDealId = $fields[$masterDealParentFieldName] ?? null;

        $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

        $deal = $dealFactory->getItem($fields['ID']);

        $previousMasterDealId = $deal->get($masterDealParentFieldName);
        if($deal->hasField($masterDealParentFieldName) && $masterDealId !== $previousMasterDealId) {
            self::addCalculateAgent($previousMasterDealId);
        }
    }

    public static function OnAfterCrmDealUpdate($fields)
    {
        /** @var \Master\Deal\Services\TypeService $typeService */
        $typeService = ServiceLocator::getInstance()->get('master.deal.types');
        $masterDealTypeId = $typeService->getMasterDealTypeId();
        $masterDealParentFieldName = "PARENT_ID_{$masterDealTypeId}";

        $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

        $deal = $dealFactory->getItem($fields['ID']);

        $masterDealId = null;
        if(is_null($masterDealId) && $deal->hasField($masterDealParentFieldName)) {
            $masterDealId = $deal->get($masterDealParentFieldName);
        }

        if(($fields['OPPORTUNITY'] !== $deal->getOpportunity() || $fields['CURRENCY_ID'] !== $deal->getCurrencyId()) && $masterDealId !== null){
            self::addCalculateAgent($masterDealId);
        }
    }

    private static function addCalculateAgent(int $masterDealId, int $offset = 0)
    {
        $next_exec = '';
        if($offset > 0){
            $currentDateTime = new \DateTime();
            $currentDateTime = $currentDateTime->modify("+$offset seconds");
            $next_exec = DateTime::createFromPhp($currentDateTime);
        }

        \CAgent::AddAgent(
            "\Master\Deal\Agents\CalculateDealSumAgent::execute({$masterDealId});",
            "master.deal",
            "N",
            1,
            "",
            "Y",
            $next_exec,
        );
    }
}