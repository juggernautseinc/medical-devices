<?php

/**
 *
 * link    http://www.open-emr.org
 * author  Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2020. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

namespace OpenEMR\Modules\Comlink;

require_once __DIR__ . '/../../../globals.php';

/**
 * @global EventDispatcher $eventDispatcher Injected by the OpenEMR module loader;
 */

$bootstrap = new Bootstrap($eventDispatcher, $GLOBALS['kernel']);
$bootstrap->subscribeToEvents();

/*
use OpenEMR\Events\Globals\GlobalsInitializedEvent;
use OpenEMR\Menu\MenuEvent;
use OpenEMR\Modules\CustomModuleSkeleton\Bootstrap;
use OpenEMR\Services\Globals\GlobalSetting;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


function comlink_add_menu_item(MenuEvent $event)
{
    $menu = $event->getMenu();

    $menuItem = new stdClass();
    $menuItem->requirement = 0;
    $menuItem->target = 'fin';
    $menuItem->menu_id = 'fin0';
    $menuItem->label = xlt("Patient Monitoring");
    $menuItem->url = "/interface/modules/custom_modules/oe-comlink-module/comlinkUI.php";
    $menuItem->children = [];
    $menuItem->acl_req = ["patients", "docs"];
    $menuItem->global_req = [];

    foreach ($menu as $item) {
        if ($item->menu_id == 'patimg') {
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
//$eventDispatcher->addListener(MenuEvent::MENU_UPDATE, 'comlink_add_menu_item');

/**
 * @throws Exception
 */
/*
function createFaxModuleGlobals(GlobalsInitializedEvent $event)
{
    $instructuname = xlt('Enter username from comlink account.');
    $instructupass = xlt('Enter password from comlink account.');
    $instructorgid = xlt('Enter organizaion identification.');
    $posturi_instruction = xlt('Enter Comlink URI');
    $event->getGlobalsService()->createSection("Comlink Device Module", "Billing");
    $setting = new GlobalSetting(xl('Comlink Device URI'), 'text', '', $posturi_instruction);
    $event->getGlobalsService()->appendToSection("Comlink Device Module", "comlink_device_uri", $setting);
    $setting = new GlobalSetting(xl('Comlink Username'), 'text', '', $instructuname);
    $event->getGlobalsService()->appendToSection("Comlink Device Module", "comlink_username", $setting);
    $setting = new GlobalSetting(xl('Comlink Password'), 'encrypted', '', $instructupass);
    $event->getGlobalsService()->appendToSection("Comlink Device Module", "comlink_password", $setting);
    $setting = new GlobalSetting(xl('Comlink Org ID'), 'text', '', $instructorgid);
    $event->getGlobalsService()->appendToSection("Comlink Device Module", "comlink_xorgid", $setting);
}

$eventDispatcher->addListener(GlobalsInitializedEvent::EVENT_HANDLE, 'createFaxModuleGlobals');
*/

