<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Result;
use Bitrix\Main\Error;
use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Main\EventManager;
use Bitrix\Crm\Relation;
use Bitrix\Crm\RelationIdentifier;
use Bitrix\Crm\Service\Container;


Loc::loadMessages(__FILE__);

class master_deal extends CModule
{
    const MASTER_DEAL_ENTITY_CODE = 'MASTER_DEAL';

    /** @var string */
    public $MODULE_ID;

    /** @var string */
    public $MODULE_VERSION;

    /** @var string */
    public $MODULE_VERSION_DATE;

    /** @var string */
    public $MODULE_NAME;

    /** @var string */
    public $MODULE_DESCRIPTION;

    /** @var string */
    public $MODULE_GROUP_RIGHTS;

    /** @var string */
    public $PARTNER_NAME;

    /** @var string */
    public $PARTNER_URI;
    protected $PARTNER_CODE;
    protected $MODULE_CODE;
    private $siteId;

    public function __construct()
    {

        $arModuleVersion = [];
        include __DIR__.'/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'master.deal';
        $this->MODULE_NAME = Loc::getMessage('MASTER_DEAL_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('MASTER_DEAL_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('MASTER_DEAL_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('MASTER_DEAL_PARTNER_URI');

        $this->MODULE_GROUP_RIGHTS = 'Y';

        $this->PARTNER_CODE = $this->getPartnerCodeByModuleID();
        $this->MODULE_CODE = $this->getModuleCodeByModuleID();
        
        $rsSites = CSite::GetList($by="sort", $order="desc", ['ACTIVE' => 'Y']);
        $arSite = $rsSites->Fetch();
        $this->siteId = $arSite['ID'];

        \Bitrix\Main\Loader::includeModule('crm');
    }

    /**
     * Получение актуального пути к модулю с учетом многосайтовости
     * Как вариант можно использовать более производительную функцию str_pos
     * Недостатком данного метода является возможность "ложных срабатываний".
     * В том случае если в пути встретится два раза последовательность
     * local/modules или bitrix/modules.
     *
     * @param bool $notDocumentRoot
     * @return mixed|string
     */
    protected function getPath($notDocumentRoot = false)
    {
        return  ($notDocumentRoot)
            ? preg_replace('#^(.*)\/(local|bitrix)\/modules#','/$2/modules',dirname(__DIR__))
            : dirname(__DIR__);
    }

    /**
     * Получение кода партнера из ID модуля
     * @return string
     */
    protected function getPartnerCodeByModuleID()
    {
        $delimeterPos = strpos($this->MODULE_ID, '.');
        $pCode = substr($this->MODULE_ID, 0, $delimeterPos);

        if (!$pCode) {
            $pCode = $this->MODULE_ID;
        }

        return $pCode;
    }

    /**
     * Получение кода модуля из ID модуля
     * @return string
     */
    protected function getModuleCodeByModuleID()
    {
        $delimeterPos = strpos($this->MODULE_ID, '.') + 1;
        $mCode = substr($this->MODULE_ID, $delimeterPos);

        if (!$mCode) {
            $mCode = $this->MODULE_ID;
        }

        return $mCode;
    }

    /**
     * Проверка версии ядра системы
     *
     * @return bool
     */
    protected function isVersionD7() {
        return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
    }

    /**
     * Установка модуля
     */
    public function DoInstall()
    {
        global $APPLICATION;

        if ($this->isVersionD7()) {

            $this->installCrmEntity();
            $this->installEvents();
            $this->installComponents();

            ModuleManager::registerModule($this->MODULE_ID);

        } else {
            $APPLICATION->ThrowException("MASTER_DEAL_INSTALL_ERROR_WRONG_VERSION");
        }

    }

    /**
     * Добавление смарт процесса и его полей
     */
    public function installCrmEntity()
    {
        global $DB, $USER;

        $result = new Result();
        $entityTypeId = TypeTable::getNextAvailableEntityTypeId();
        $connection = Application::getConnection();
        $errors = [];

        $typeRes = TypeTable::getList([
            'select' => ['ID'],
            'order' => ['ID' => 'DESC'],
            'limit' => 1
        ]);

        if($lastEntity = $typeRes->fetch()){
            $typeId = ((int)$lastEntity['ID'])+1;
        } else {
            $typeId = 1;
        }



        include __DIR__ . '/userField/install.php';

        $sql_path = __DIR__ . '/db/mysql/bitrix_crm_entity_mailing_settings.sql';

        $sql = file_get_contents($sql_path);
        $sql = str_replace(['{ENTITY_TYPE_ID}', '{TYPE_ID}'],[$entityTypeId, $typeId], $sql);

        $addResult = TypeTable::add([
            'ID' => $typeId,
            'ENTITY_TYPE_ID' => $entityTypeId,
            'CODE' => self::MASTER_DEAL_ENTITY_CODE,
            'NAME' => self::MASTER_DEAL_ENTITY_CODE,
            'TITLE' => 'Мастер сделка',
            'TABLE_NAME' => 'b_crm_dynamic_items_'.$entityTypeId,
            'CREATED_BY' => $USER->GetID(),
            'IS_CATEGORIES_ENABLED' => 'N',
            'IS_STAGES_ENABLED' => 'N',
            'IS_BEGIN_CLOSE_DATES_ENABLED' => 'N',
            'IS_CLIENT_ENABLED' => 'Y',
            'IS_LINK_WITH_PRODUCTS_ENABLED' => 'N',
            'IS_CRM_TRACKING_ENABLED' => 'N',
            'IS_MYCOMPANY_ENABLED' => 'N',
            'IS_DOCUMENTS_ENABLED' => 'Y',
            'IS_SOURCE_ENABLED' => 'N',
            'IS_USE_IN_USERFIELD_ENABLED' => 'Y',
            'IS_OBSERVERS_ENABLED' => 'N',
            'IS_RECYCLEBIN_ENABLED' => 'Y',
            'IS_AUTOMATION_ENABLED' => 'Y',
            'IS_BIZ_PROC_ENABLED' => 'Y',
            'IS_SET_OPEN_PERMISSIONS' => 'Y',
            'IS_PAYMENTS_ENABLED' => 'N',
            'IS_COUNTERS_ENABLED' => 'N'
        ]);

        if(!$addResult->isSuccess()){
            $result->addErrors($result->getErrorMessages());
            return $result;
        }

        foreach ($connection->parseSqlBatch($sql) as $strSql){
            if(!$DB->Query($strSql, true))
            {
                $result->addError(new Error(Loc::getMessage('MASTER_DEAL_ERROR_DATABASE_QUERY').' '.$DB->GetErrorMessage()));
            }
        }

        $this->setEntityRelations();
    }

    /**
     * Насройка прослушивания событий
     * @return void
     */
    public function installEvents()
    {
        EventManager::getInstance()->registerEventHandler(
            'crm',
            'onCrmDynamicItemAdd',
            $this->MODULE_ID,
            '\Master\Deal\Crm\Handlers\DynamicItem',
            'OnCrmDynamicItemAdd'
        );

        EventManager::getInstance()->registerEventHandler(
            'crm',
            'onCrmDynamicItemUpdate',
            $this->MODULE_ID,
            '\Master\Deal\Crm\Handlers\DynamicItem',
            'OnCrmDynamicItemUpdate'
        );

        EventManager::getInstance()->registerEventHandler(
            'crm',
            'OnAfterCrmDealAdd',
            $this->MODULE_ID,
            '\Master\Deal\Crm\Handlers\DealHandler',
            'OnAfterCrmDealAdd'
        );

        EventManager::getInstance()->registerEventHandler(
            'crm',
            'OnBeforeCrmDealUpdate',
            $this->MODULE_ID,
            '\Master\Deal\Crm\Handlers\DealHandler',
            'OnBeforeCrmDealUpdate'
        );

        EventManager::getInstance()->registerEventHandler(
            'crm',
            'OnAfterCrmDealUpdate',
            $this->MODULE_ID,
            '\Master\Deal\Crm\Handlers\DealHandler',
            'OnAfterCrmDealUpdate'
        );

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\Master\Deal\UserField\MoneyRubType',
            'GetUserTypeDescription'
        );
    }

    /**
     * Копирование компонентов
     * @return void
     */
    public function installComponents()
    {
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/local/modules/master.deal/install/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components', true, true);
    }

    /**
     * Удаление модуля
     */
    public function DoUnInstall()
    {
        $this->unInstallEvents();
        $this->unInstallCrmEntity();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function unInstallCrmEntity()
    {
        try {
            if($entityMasterDeal = $this->getEntityByCode(self::MASTER_DEAL_ENTITY_CODE)){
                $this->removeDataRows($entityMasterDeal['ENTITY_TYPE_ID']);
                $controller = new \Bitrix\Crm\Controller\Type();
                $type = \Bitrix\Crm\Service\Container::getInstance()->getTypeByEntityTypeId($entityMasterDeal['ENTITY_TYPE_ID']);
                $controller->deleteAction($type);
            }
        } catch(\Bitrix\Main\DB\SqlQueryException $exception) {


        }
    }
    public function unInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler(
            'crm',
            'onCrmDynamicItemAdd',
            $this->MODULE_ID,
            '\Master\Deal\Crm\Handlers\DynamicItem',
            'OnCrmDynamicItemAdd'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'crm',
            'onCrmDynamicItemUpdate',
            $this->MODULE_ID,
            '\Master\Deal\Crm\Handlers\DynamicItem',
            'OnCrmDynamicItemUpdate'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'crm',
            'OnBeforeCrmDealAdd',
            $this->MODULE_ID,
            '\Master\Deal\Crm\Handlers\DealHandler',
            'OnBeforeCrmDealAdd'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'crm',
            'OnBeforeCrmDealUpdate',
            $this->MODULE_ID,
            '\Master\Deal\Crm\Handlers\DealHandler',
            'OnBeforeCrmDealUpdate'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\Master\Deal\UserField\MoneyRubType',
            'GetUserTypeDescription'
        );
    }

    private function getEntityByCode($code)
    {
        $arEntity = TypeTable::getList([
            'select' => ['ENTITY_TYPE_ID',"ID"],
            'filter' => [
                '=CODE' => $code
            ],
        ])->fetch();

        if ($arEntity){
            return $arEntity;
        }

        return null;
    }

    private function setEntityRelations()
    {
        $result = new Result();

        if($entityMasterDeal = $this->getEntityByCode(self::MASTER_DEAL_ENTITY_CODE)){

           $relationManager = Container::getInstance()->getRelationManager();
           $relationIdentifier = new RelationIdentifier($entityMasterDeal['ENTITY_TYPE_ID'], \CCrmOwnerType::Deal);

           $result = $relationManager->bindTypes(
               new Relation(
                   $relationIdentifier,
                   (new Relation\Settings())
                       ->setRelationType(Relation\RelationType::BINDING)
                       ->setIsChildrenListEnabled(true)
                   ,
               )
           );
        }

        return $result;
    }

    public function removeDataRows($entity): void
    {
        $factory = \Bitrix\Crm\Service\Container::getInstance()->getFactory($entity);

        $items = $factory->getItems();


        if(!empty($items)){
            foreach ($items as $item){
                $operation = $factory->getDeleteOperation($item)
                    ->disableAllChecks();
                $operation->launch();
            }
        }
    }
 }


