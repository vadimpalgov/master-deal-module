<?php
return [
    'controllers' => array(
        'value' => array(
            'defaultNamespace' => '\\Master\\Deal\\Controllers',
        ),
        'readonly' => true,
    ),
    'services' => [
        'value' => [
            'master.deal' => [
                'className' => \Master\Deal\Services\DealService::class,
            ],
            'master.deal.field' => [
                'className' => \Master\Deal\Services\FieldService::class,
            ],
            'master.deal.types' => [
                'className' => \Master\Deal\Services\TypeService::class,
            ],
            'master.deal.name' => [
                'className' => \Master\Deal\Services\NameService::class,
            ],
            'master.deal.sum' => [
                'className' => \Master\Deal\Services\SumService::class,
            ],
        ]
    ]
];