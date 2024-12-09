<?php

namespace Master\Deal\Services;

use Bitrix\Crm\Item;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\DI\ServiceLocator;

class DealService
{
    /**
     * Получает мастер-сделку по ее ID.
     *
     * @param int $masterDealId Идентификатор мастер-сделки.
     * @return Item|null Возвращает объект Item, если сделка найдена, иначе null.
     */
    public function getMasterDealById(int $masterDealId): ?Item
    {
        // Получаем сервис для работы с типами сделок.
        /** @var \Master\Deal\Services\TypeService $typeService */
        $typeService = ServiceLocator::getInstance()->get('master.deal.types');

        // Проверяем наличие типа сделки и фабрики для данного типа.
        if ($typeService && $factory = Container::getInstance()->getFactory($typeService->getMasterDealTypeId())) {
            // Возвращаем элемент (сделку) из фабрики.
            return $factory->getItem($masterDealId);
        }

        return null;
    }
}