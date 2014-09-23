<?php

namespace Sample\CsvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prices
 */
class Prices
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $generalId;

    /**
     * @var integer
     */
    private $periodId;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $status;

    /**
     * @var boolean
     */
    private $isActive;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @var \Sample\CsvBundle\Entity\General
     */
    private $general;

    /**
     * @var \Sample\CsvBundle\Entity\Periods
     */
    private $period;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set generalId
     *
     * @param integer $generalId
     * @return Prices
     */
    public function setGeneralId($generalId)
    {
        $this->generalId = $generalId;

        return $this;
    }

    /**
     * Get generalId
     *
     * @return integer 
     */
    public function getGeneralId()
    {
        return $this->generalId;
    }

    /**
     * Set periodId
     *
     * @param integer $periodId
     * @return Prices
     */
    public function setPeriodId($periodId)
    {
        $this->periodId = $periodId;

        return $this;
    }

    /**
     * Get periodId
     *
     * @return integer 
     */
    public function getPeriodId()
    {
        return $this->periodId;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Prices
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Prices
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Prices
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Prices
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     * @return Prices
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime 
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set general
     *
     * @param \Sample\CsvBundle\Entity\General $general
     * @return Prices
     */
    public function setGeneral(\Sample\CsvBundle\Entity\General $general = null)
    {
        $this->general = $general;

        return $this;
    }

    /**
     * Get general
     *
     * @return \Sample\CsvBundle\Entity\General 
     */
    public function getGeneral()
    {
        return $this->general;
    }

    /**
     * Set period
     *
     * @param \Sample\CsvBundle\Entity\Periods $period
     * @return Prices
     */
    public function setPeriod(\Sample\CsvBundle\Entity\Periods $period = null)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return \Sample\CsvBundle\Entity\Periods 
     */
    public function getPeriod()
    {
        return $this->period;
    }
    
    /**
     * Now we tell doctrine that before we persist or update we call the
     * updatedTimestamps() function.
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function timestamps()
    {
        $this->setModifiedAt(new \DateTime(date('Y-m-d H:i:s')));
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        }
    }
}
