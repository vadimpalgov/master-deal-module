<?php

namespace Master\Deal\Services;

use Bitrix\Crm\Item;
use Bitrix\Crm\Service\Container;
use Master\Deal\Tools\ServiceTrait;

class SumService
{
    use ServiceTrait;

    private ?TypeService $typeService;  // Сервис для работы с типами сделок
    private ?FieldService $fieldService;  // Сервис для работы с полями сделки

    /**
     * Конструктор класса. Инициализирует сервисы.
     */
    public function __construct()
    {
        $this->typeService = $this->getTypeService();  // Получаем сервис для работы с типами сделок
        $this->fieldService = $this->getFieldService();  // Получаем сервис для работы с полями сделки
    }

    /**
     * Рассчитывает сумму по сделкам и обновляет поле суммы в мастер-сделке.
     *
     * @param Item $masterDeal Объект мастер-сделки, для которой выполняется расчет.
     */
    public function calculateAndUpdate(Item $masterDeal)
    {
        $sum = 0;

        // Получаем дочерние сделки мастер-сделки
        if ($deals = $this->getChildrenDeals($masterDeal->getId())) {

            foreach ($deals as $deal) {

                // Если валюта сделки не рубли, конвертируем сумму в рубли
                if ($deal->getCurrencyId() !== 'RUB') {
                    $sum += $this->convertCurrencyAmountToRub($deal->getOpportunity(), $deal->getCurrencyId());
                } else {
                    // Если валюта сделки рубли, просто добавляем сумму
                    $sum += $deal->getOpportunity();
                }
            }
        }

        // Получаем имя поля для суммы
        $sumFieldName = $this->fieldService->getSumField();

        // Если поле существует в мастер-сделке, обновляем его значение
        if ($masterDeal->hasField($sumFieldName)) {
            $masterDeal->set($sumFieldName, $sum);
        }

        // Если поле изменилось, сохраняем мастер-сделку
        if ($masterDeal->isChanged($sumFieldName)) {
            $masterDeal->save();
        }
    }

    /**
     * Получает дочерние сделки для мастер-сделки по ее ID.
     *
     * @param int $masterDealId ID мастер-сделки.
     * @return Item\Deal[] Массив дочерних сделок.
     */
    private function getChildrenDeals($masterDealId): array
    {
        // Получаем фабрику для работы с сделками
        $dealFactory = Container::getInstance()->getFactory(\CCrmOwnerType::Deal);

        // Формируем имя поля для фильтрации по родительскому ID
        $parentField = 'PARENT_ID_' . $this->typeService->getMasterDealTypeId();

        // Возвращаем дочерние сделки, отфильтрованные по родительскому ID
        return $dealFactory->getItems([
            'filter' => [
                $parentField => $masterDealId
            ]
        ]);
    }

    /**
     * Конвертирует сумму в валюте сделки в рубли.
     *
     * @param float $amount Сумма сделки.
     * @param string $currency Код валюты.
     * @return int|float Конвертированная сумма в рублях.
     */
    private function convertCurrencyAmountToRub($amount, string $currency): int|float
    {
        // Получаем курс валюты
        if ($currencyData = \CCrmCurrency::GetByID($currency)) {
            // Конвертируем сумму в рубли, округляя до 2 знаков после запятой
            return round($amount * $currencyData['AMOUNT'], 2);
        }

        return 0;
    }
}