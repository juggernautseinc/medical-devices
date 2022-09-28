<?php

/*
 *  package   Comlink OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2022. Sherwin Gaddis <sherwingaddis@gmail.com>
 *
 */

namespace OpenEMR\Modules\Comlink;

require_once "Database.php";

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

    public function __construct()
    {
        //do epic stuff
    }

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
