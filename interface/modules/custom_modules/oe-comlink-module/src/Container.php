<?php

/*
 *
 *  @package       OpenEMR
 *  @link    https://www.open-emr.org https://affordablecustomehr.com
 *  @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  @copyright Copyright (c) 2022 Sherwin Gaddis <sherwingaddis@gmail.com>
 *  @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace Comlink\OpenEMR\Module;


/**
 * Class Container
 * @package OpenEMR\Modules\Comlink
 */

class Container
{
    /**
     * @var
     */
    private $database;

    /**
     * @return mixed
     */
    public function getDatabase()
    {
        if ($this->database === null) {
            $this->database = new Database();
        }
        return $this->database;
    }
}
