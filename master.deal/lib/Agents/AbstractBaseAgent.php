<?php

namespace Master\Deal\Agents;

abstract class AbstractBaseAgent
{
    public static function execute(): string
    {
        $args = func_get_args();

        // Передаем массив аргументов в другой метод, распаковывая его
        $classMethod = (new static());

        call_user_func_array($classMethod, $args);

        return self::getFunctionString(__METHOD__, func_get_args());

        #TODO: Добавить удаление разовых агентов
    }

    /**
     * @param string $method
     * @param string $params
     * @return string
     */
    protected static function getFunctionString(string $method, array $params = []): string
    {
        if($params){
            $strParams = implode(', ', $params);
        } else {
            $strParams = '';
        }

        return "{$method}({$strParams});";
    }
}