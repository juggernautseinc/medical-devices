<?php

/**
 *
 *  package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */


use OpenEMR\Menu\MenuEvent;
use Symfony\Component\EventDispatcher\Event;
use OpenEMR\Events\PatientReport\PatientReportEvent;
use OpenEMR\Events\PatientDocuments\PatientDocumentEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use OpenEMR\Services\Globals\GlobalSetting;

function oe_module_faxsms_add_menu_item(MenuEvent $event)
{
    $menu = $event->getMenu();

    $menuItem = new stdClass();
    $menuItem->requirement = 0;
    $menuItem->target = 'mod';
    $menuItem->menu_id = 'mod0';
    $menuItem->label = xlt("Fax Account Summary");
    $menuItem->url = "/interface/modules/custom_modules/oe-module-documo-fax/settings.php";
    $menuItem->children = [];
    $menuItem->acl_req = ["patients", "docs"];
    $menuItem->global_req = ["oedocumofax_enable"]; 

    foreach ($menu as $item) {
        if ($item->menu_id == 'modimg') {
            $item->children[] = $menuItem;
            break;
        }
    }

    $event->setMenu($menu);

    return $event;
}

function oe_module_documofax_add_menu_item(MenuEvent $event)
{
    $menu = $event->getMenu();

    $menuItem = new stdClass();
    $menuItem->requirement = 0;
    $menuItem->target = 'mod';
    $menuItem->menu_id = 'mod0';
    $menuItem->label = xlt("Fax History");
    $menuItem->url = "/interface/modules/custom_modules/oe-module-documo-fax/fax/faxque.php";
    $menuItem->children = [];
    $menuItem->acl_req = ["patients", "docs"];
    $menuItem->global_req = [];

    foreach ($menu as $item) {
        if ($item->menu_id == 'misimg') {
            $item->children[] = $menuItem;
            break;
        }
    }

    $event->setMenu($menu);

    return $event;
}

/**
 * @var EventDispatcherInterface $eventDispatcher
 * @var array                    $module
 * @global                       $eventDispatcher @see ModulesApplication::loadCustomModule
 * @global                       $module          @see ModulesApplication::loadCustomModule
 */

function createFaxModuleGlobals(GlobalsInitializedEvent $event)
{
    $instruct = xl('Obtain API Key to install service');
    $event->getGlobalsService()->createSection("Fax Module", "Report");
    $setting = new GlobalSetting(xl('Documo Fax API Key'), 'encrypted', '', $instruct);
    $event->getGlobalsService()->appendToSection("Fax Module", "oedocumofax_enable", $setting);
}

$eventDispatcher->addListener(MenuEvent::MENU_UPDATE, 'oe_module_faxsms_add_menu_item');
$eventDispatcher->addListener(MenuEvent::MENU_UPDATE, 'oe_module_documofax_add_menu_item');
$eventDispatcher->addListener(GlobalsInitializedEvent::EVENT_HANDLE, 'createFaxModuleGlobals');


$eventDispatcher->addListener(PatientReportEvent::ACTIONS_RENDER_POST, 'oe_module_documo_fax_render_action_anchors');
$eventDispatcher->addListener(PatientReportEvent::JAVASCRIPT_READY_POST, 'oe_module_documo_patient_report_render_javascript_post_load');

// patient document fax anchor

function oe_module_documo_fax_render_action_anchors(Event $event)
{
?>
    <button type="button" class="genfax btn btn-secondary btn-send-msg" value="<?php echo xla('Que Fax'); ?>"><?php echo xlt('Que Fax'); ?></button><span id="waitplace"></span>
    <input type='hidden' name='fax' value='0'>
<?php
}

function oe_module_documo_patient_report_render_javascript_post_load(Event $event)
{
    ?>
    function getFaxContent() {
    top.restoreSession();
    document.report_form.fax.value = 1;
    let url = 'custom_report.php';
    let wait = '<span id="wait"><?php echo xlt("Building Document .. "); ?><i class="fa fa-cog fa-spin fa-2x"></i></span>';
    $("#waitplace").append(wait);
    $.ajax({
    type: "POST",
    url: url,
    data: $("#report_form").serialize(),
    success: function (content) {
    document.report_form.fax.value = 0;
    let btnClose = <?php echo xlj("Close"); ?>;
    let title = <?php echo xlj("Send To Contact"); ?>;
    let url = top.webroot_url + '/interface/modules/custom_modules/oe-module-documo-fax/fax/quefax.php?isContent=0&file=' + content;
    dlgopen(url, '', 'modal-md', 625, '', title, {buttons: [{text: btnClose, close: true, style: 'secondary'}]});
    return false;
    }
    }).always(function () {
    $("#wait").remove();
    });
    return false;
    }
    $(".genfax").click(function() {getFaxContent();});
    <?php
}

// patient documents fax anchor
function oe_module_documo_document_render_action_anchors(Event $event)
{
    ?>
    <a class="btn btn-secondary btn-send-msg" href="" onclick="return doFax(event,file,mime)"><span><?php echo xlt('Send Fax'); ?></span></a>
    <?php
}
function oe_module_documofax_document_render_javascript_fax_dialog(Event $event)
{
    ?>
    function doFax(e, filePath, mime='') {
    e.preventDefault();
    let btnClose = <?php echo xlj("Close"); ?>;
    let title = <?php echo xlj("Send To Contact"); ?>;
    let url = top.webroot_url +
    '/interface/modules/custom_modules/oe-module-documo-fax/fax/quefax.php?isDocuments=true&file=' + filePath +
    '&mime=' + mime;
    dlgopen(url, 'faxto', 'modal-md', 650, '', title, {buttons: [{text: btnClose, close: true, style: 'primary'}]});
    return false;
    }
    <?php
}
$eventDispatcher->addListener(PatientDocumentEvent::ACTIONS_RENDER_FAX_ANCHOR, 'oe_module_documo_document_render_action_anchors');
$eventDispatcher->addListener(PatientDocumentEvent::JAVASCRIPT_READY_FAX_DIALOG, 'oe_module_documofax_document_render_javascript_fax_dialog');
?>
