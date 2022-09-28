<?php

/**
 * API index page for receiving requests from the OpenEMR clinician requests.
 *
 * @package openemr
 * @link      http://www.open-emr.org
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Comlink
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

// since we are working inside the portal we have to use the portal session verification logic here...
require_once "../../../../globals.php";

use Comlink\OpenEMR\Modules\TeleHealthModule\Bootstrap;

$kernel = $GLOBALS['kernel'];
$bootstrap = new Bootstrap($kernel->getEventDispatcher(), $kernel);
$roomController = $bootstrap->getTeleconferenceRoomController(false);

$action = $_REQUEST['action'] ?? '';
$queryVars = $_REQUEST ?? [];
$queryVars['pid'] = $_SESSION['pid'] ?? null;
$queryVars['authUser'] = $_SESSION['authUser'] ?? null;
$roomController->dispatch($action, $queryVars);
exit;
