<?php

use Master\Deal\UserField\UserFieldFactory;

include_once __DIR__ . '/../../lib/UserField/UserFieldFactory.php';
include_once __DIR__ . '/../../lib/UserField/MoneyRubType.php';

AddEventHandler(
    "main",
    "OnUserTypeBuildList",
    [
        "\Master\Deal\UserField\MoneyRubType",
        "GetUserTypeDescription"
    ]
);

$addedFields = [];

/**
 * @var int $typeId
 */

$addedFields['CRM_MASTER_DEAL_NUMBER'] = UserFieldFactory::add(
    'CRM_MASTER_DEAL_NUMBER',
    'Номер Мастер Сделки',
    'Master deal number',
    'CRM_'.$typeId,
    UserFieldFactory::TYPE_STRING
);

$addedFields['CRM_MASTER_DEAL_END_USER'] = UserFieldFactory::add(
    'CRM_MASTER_DEAL_END_USER',
    'Конечный заказчик',
    'End user',
    'CRM_'.$typeId,
    UserFieldFactory::TYPE_CRM,
    true,
    false,
    ['COMPANY' => 'Y', 'LEAD' => null]
);

$addedFields['CRM_MASTER_DEAL_SUM_CURRENCY'] = UserFieldFactory::add(
    'CRM_MASTER_DEAL_SUM_CURRENCY',
    'Сумма и валюта',
    'Summary currency',
    'CRM_'.$typeId,
    UserFieldFactory::TYPE_MONEY,
    false,
    false,
    ['DEFAULT_VALUE' => 0]
);

$addedFields['CRM_MASTER_DEAL_COUNTRY'] = UserFieldFactory::add(
    'CRM_MASTER_DEAL_COUNTRY',
    'Страна реализации',
    'Country',
    'CRM_'.$typeId,
    UserFieldFactory::TYPE_STRING,
    true,
);

$addedFields['CRM_MASTER_DEAL_GEO_REGION'] = UserFieldFactory::add(
    'CRM_MASTER_DEAL_GEO_REGION',
    'Регион (географический)',
    'Region',
    'CRM_'.$typeId,
    UserFieldFactory::TYPE_STRING,
);

$addedFields['CRM_MASTER_DEAL_SE_REGION'] = UserFieldFactory::add(
    'CRM_MASTER_DEAL_SE_REGION',
    'Регион (СЭ)',
    'Region SE',
    'CRM_'.$typeId,
    UserFieldFactory::TYPE_STRING,
);

$addedFields['CRM_MASTER_DEAL_CITY_OBJECT'] = UserFieldFactory::add(
    'CRM_MASTER_DEAL_CITY_OBJECT',
    'Населенный пункт объекта',
    'Locality of the object',
    'CRM_'.$typeId,
    UserFieldFactory::TYPE_STRING,
);

$addedFields['CRM_MASTER_DEAL_INVESTMENTS_SUM'] = UserFieldFactory::add(
    'CRM_MASTER_DEAL_INVESTMENTS_SUM',
    'Сумма инвестиций (руб.)',
    'Sum',
    'CRM_'.$typeId,
    UserFieldFactory::TYPE_MONEY_RUB,
    false,
    false,
    ['DEFAULT_VALUE' => 0]
);
