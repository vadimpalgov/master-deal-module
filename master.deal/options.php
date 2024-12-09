<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var $APPLICATION CMain
 */

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Master\Deal\Services\FieldService;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);

$options = [
    Loc::getMessage("MASTER_DEAL_NAME_SETTINGS"),
    array(
        "MASTER_DEAL_NAME_TEMPLATE",
        Loc::getMessage("MASTER_DEAL_NAME_TEMPLATE"),
        "",
        array("text")
    )
];

if($fields = FieldService::getFieldList()){
    $options[] = Loc::getMessage("MASTER_DEAL_FIELD_SETTINGS");
    foreach ($fields as $field) {
        $options[] = array(
            "MASTER_DEAL_FIELD_{$field}",
            Loc::getMessage("MASTER_DEAL_FIELD_{$field}"),
            $field,
            array("text")
        );
    }
}

$aTabs = array(
    array(
        "DIV"       => "edit",
        "TAB"       => Loc::getMessage("MASTER_DEAL_MAIN_OPTIONS_TAB_NAME"),
        "TITLE"   => Loc::getMessage("MASTER_DEAL_MAIN_OPTIONS_TAB_NAME"),
        "OPTIONS" => $options
    )
);

if($request->isPost() && check_bitrix_sessid()){

    foreach($aTabs as $aTab){

        foreach($aTab["OPTIONS"] as $arOption){
            if(!is_array($arOption)){
                continue;
            }

            if($arOption["note"]){
                continue;
            }

            if($request["apply"]){
                $optionValue = $request->getPost($arOption[0]);

                if($arOption[0] == "switch_on"){
                    if($optionValue == ""){
                        $optionValue = "N";
                    }
                }
                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
            }elseif($request["default"]){

                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }

    LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
}

$tabControl = new CAdminTabControl(
    "tabControl",
    $aTabs
);

$tabControl->Begin();
?>
<form action="<?=($APPLICATION->GetCurPage()); ?>?mid=<?=($module_id); ?>&lang=<?=(LANG); ?>" method="post">

    <?php
    foreach($aTabs as $aTab){

        if($aTab["OPTIONS"]){

            $tabControl->BeginNextTab();

            __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
        }
    }

    $tabControl->Buttons();
    ?>

    <input type="submit" name="apply" value="<?=(Loc::GetMessage("MASTER_DEAL_OPTIONS_INPUT_APPLY")); ?>" class="adm-btn-save" />
    <input type="submit" name="default" value="<?=(Loc::GetMessage("MASTER_DEAL_OPTIONS_INPUT_DEFAULT")); ?>" />

    <?php
    echo(bitrix_sessid_post());
    ?>

</form>
<?php

$tabControl->End();