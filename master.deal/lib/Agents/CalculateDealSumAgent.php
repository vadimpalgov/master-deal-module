<?php

namespace Master\Deal\Agents;

use Bitrix\Main\DI\ServiceLocator;

class CalculateDealSumAgent extends AbstractBaseAgent
{
    public function __invoke($masterDealId)
    {   ray(['CalculateDealSumAgent' => $masterDealId]);
        /** @var \Master\Deal\Services\DealService $dealService */
        $dealService = ServiceLocator::getInstance()->get('master.deal');

        /** @var \Master\Deal\Services\SumService $sumService */
        $sumService = ServiceLocator::getInstance()->get('master.deal.sum');

        if($masterDeal = $dealService->getMasterDealById($masterDealId)) {
            $sumService->calculateAndUpdate($masterDeal);
        }
    }
}