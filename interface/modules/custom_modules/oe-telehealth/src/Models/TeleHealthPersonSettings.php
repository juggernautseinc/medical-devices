<?php

namespace Comlink\OpenEMR\Modules\TeleHealthModule\Models;

class TeleHealthPersonSettings
{
    private $id;
    private $isPatient;
    private $dbRecordId;
    /**
     * @var \DateTime
     */
    private $dateCreated;

    /**
     * @var \DateTime
     */
    private $dateRegistered;

    /**
     * @var \DateTime
     */
    private $dateUpdated;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * TeleHealthPersonSettings constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return TeleHealthPersonSettings
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPatient()
    {
        return $this->isPatient;
    }

    /**
     * @param mixed $isPatient
     * @return TeleHealthPersonSettings
     */
    public function setIsPatient($isPatient)
    {
        $this->isPatient = $isPatient;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDbRecordId()
    {
        return $this->dbRecordId;
    }

    /**
     * @param mixed $dbRecordId
     * @return TeleHealthPersonSettings
     */
    public function setDbRecordId($dbRecordId)
    {
        $this->dbRecordId = $dbRecordId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated(): \DateTime
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     * @return TeleHealthPersonSettings
     */
    public function setDateCreated(\DateTime $dateCreated): TeleHealthPersonSettings
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateRegistered(): \DateTime
    {
        return $this->dateRegistered;
    }

    /**
     * @param \DateTime $dateRegistered
     * @return TeleHealthPersonSettings
     */
    public function setDateRegistered(\DateTime $dateRegistered): TeleHealthPersonSettings
    {
        $this->dateRegistered = $dateRegistered;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated(): \DateTime
    {
        return $this->dateUpdated;
    }

    /**
     * @param \DateTime $dateUpdated
     * @return TeleHealthPersonSettings
     */
    public function setDateUpdated(\DateTime $dateUpdated): TeleHealthPersonSettings
    {
        $this->dateUpdated = $dateUpdated;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param mixed $isEnabled
     * @return TeleHealthPersonSettings
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }
}
