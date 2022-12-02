<?php

/*
 *
 * @package      OpenEMR
 * @link               https://www.open-emr.org
 *
 * @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright Copyright (c) 2021 Sherwin Gaddis <sherwingaddis@gmail.com>
 * All Rights Reserved
 *
 */


use OpenEMR\Menu\MenuEvent;
use OpenEMR\Menu\PatientMenuEvent;
use OpenEMR\Events\PatientDemographics\RenderEvent;
use OpenEMR\Common\Csrf\CsrfUtils;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

function oe_module_priorauth_add_menu_item(MenuEvent $event)
{
    $menu = $event->getMenu();

    $menuItem = new stdClass();
    $menuItem->requirement = 0;
    $menuItem->target = 'mod';
    $menuItem->menu_id = 'mod0';
    $menuItem->label = xlt("Prior Authorization Report");
    $menuItem->url = "/interface/modules/custom_modules/oe-module-prior-authorizations/public/reports/list_report.php";
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

function oe_module_priorauth_patient_menu_item(PatientMenuEvent $menuEvent)
{
    $menu = file_get_contents(__DIR__ . '/public/patient_menu/custom_patient_menu.json');
    $menu_parsed = json_decode($menu);
    $menuEvent->setMenu($menu_parsed);
    return $menuEvent;
}

function renderButtonPostLoad(Event $event) {
    ?>
    document
            .getElementById('addButton')
            .addEventListener("click", function (e){
                if( ! confirm('Do you really want to do this?')){
                     e.preventDefault();
                } else {
                alert('Ok, lets do this! Click ok to really mark inactive.');
                let libUrl = 'patient_status.php';
                let pid = <?php echo $_SESSION['pid']; ?>;
                let csrf = <?php echo xlj(CsrfUtils::collectCsrfToken()); ?>;
            $.ajax({
                type: "POST",
                url: libUrl,
                data: {patientid: pid, csrf_token: csrf},
                error: function (qXHR) {
                console.log("There was an error");
                alert(<?php echo xlj("File Error") ?> +"\n" + id)
            },
            success: function (result) {
                alert(result);
            }
        });
        }
    });
<?php
}

/**
 * @var EventDispatcherInterface $eventDispatcher
 * @var array                    $module
 * @global                       $eventDispatcher @see ModulesApplication::loadCustomModule
 * @global                       $module          @see ModulesApplication::loadCustomModule
 */

$eventDispatcher->addListener(MenuEvent::MENU_UPDATE, 'oe_module_priorauth_add_menu_item');
$eventDispatcher->addListener(PatientMenuEvent::MENU_UPDATE, 'oe_module_priorauth_patient_menu_item');
//$eventDispatcher->addListener(RenderEvent::EVENT_RENDER_JAVA, 'renderButtonPostLoad');
