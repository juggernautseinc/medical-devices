<?php

/*
 * package   OpenEMR
 *  link      http://www.open-emr.org
 *  author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *  copyright Copyright (c )2021. Sherwin Gaddis <sherwingaddis@gmail.com>
 *  license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace OpenEMR\Modules\Documo;

require_once "ApiDispatcher.php";
require_once "Database.php";

class Provisioning
{

    /**
     * @var ApiDispatcher
     */
    private $dispatch;
    /**
     * @var
     */
    private $areaCode;
    /**
     * @var
     */
    private $state;
    /**
     * @var
     */
    private $zipcode;

    /**
     * @var
     */
    private $type;
    /**
     * @var
     */
    private $numbers;
    /**
     * @var
     */
    private $quantity;
    /**
     * @var
     */
    private $accountId;


    public function __construct()
    {
        $this->dispatch = new ApiDispatcher();
    }

    public function seekNumber()
    {
        if (isset($this->areaCode)) {
            return $this->dispatch->findAvailableFaxNumber(
                $this->areaCode,
                $this->state,
                $this->zipcode
            );
        } else {
            return 'Error area code is missing';
        }
    }

    public function numberProvisioning()
    {
        $faxnumbers = "type=" . $this->type . "&numbers=" . $this->numbers ."&quantity=" . $this->quantity . "&accountId=" . $this->accountId;

        if (isset($this->type)) {
            return $this->dispatch->registerSelectedNumbers($faxnumbers);
        } else {
            return "Error order type is missing";
        }
    }

    /**
     * @param $value
     * @return string
     */
    public function setAreaCode($value) : int
    {
       return $this->areaCode = $value;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): string
    {
        return $this->state = $state;
    }

    /**
     * @param mixed $zipcode
     */
    public function setZipcode($zipcode): int
    {
        return $this->zipcode = $zipcode;
    }

    //Make provisioning API call
    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this->type;
    }

    /**
     * @param mixed $numbers
     */
    public function setNumbers($numbers)
    {
        $this->numbers = $numbers;
        return $this->numbers;
    }

    /**
     * @param mixed $quanity
     */
    public function setQuanity($quantity)
    {
        $this->quantity = $quantity;
        return $this->quantity;
    }

    /**
     * @param mixed $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
        return $this->accountId;
    }

    /**
     * @param mixed $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this->storage;
    }
}
