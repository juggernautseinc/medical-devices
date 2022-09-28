<?php

/**
 * phpunit bootstrap file
 *
 * @package openemr
 * @link      http://www.open-emr.org
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Comlink
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

declare(strict_types=1);

$_GET['site'] = 'default';
$ignoreAuth = true;
require_once(__DIR__ . "/../../../../globals.php");
