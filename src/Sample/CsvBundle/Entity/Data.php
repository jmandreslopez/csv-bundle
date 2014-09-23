<?php

namespace Sample\CsvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Data
 */
class Data
{
    public static $multipleHeader = 'Multiple';
    public static $decimalNumbers = 2;
    public static $decimalSymbol = '.';
    public static $thousandSymbol = ',';
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $sourceId;

    /**
     * @var integer
     */
    private $fileId;

    /**
     * @var integer
     */
    private $periodId;

    /**
     * @var string
     */
    private $topicId;

    /**
     * @var float
     */
    private $price;

    /**
     * @var string
     */
    private $headers;

    /**
     * @var string
     */
    private $values;

    /**
     * @var boolean
     */
    private $isMultiple;

    /**
     * @var integer
     */
    private $multipleId;

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
     * @var \Sample\CsvBundle\Entity\Sources
     */
    private $source;

    /**
     * @var \Sample\CsvBundle\Entity\Files
     */
    private $file;

    /**
     * @var \Sample\CsvBundle\Entity\Periods
     */
    private $period;

    /**
     * @var \Sample\CsvBundle\Entity\Multiple
     */
    private $multiple;


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
     * Set sourceId
     *
     * @param integer $sourceId
     * @return Data
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;

        return $this;
    }

    /**
     * Get sourceId
     *
     * @return integer 
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * Set fileId
     *
     * @param integer $fileId
     * @return Data
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;

        return $this;
    }

    /**
     * Get fileId
     *
     * @return integer 
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * Set periodId
     *
     * @param integer $periodId
     * @return Data
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
     * Set topicId
     *
     * @param string $topicId
     * @return Data
     */
    public function setTopicId($topicId)
    {
        $this->topicId = $topicId;

        return $this;
    }

    /**
     * Get topicId
     *
     * @return string 
     */
    public function getTopicId()
    {
        return $this->topicId;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Data
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
     * Set headers
     *
     * @param string $headers
     * @return Data
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Get headers
     *
     * @return string 
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set values
     *
     * @param string $values
     * @return Data
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return string 
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set isMultiple
     *
     * @param boolean $isMultiple
     * @return Data
     */
    public function setIsMultiple($isMultiple)
    {
        $this->isMultiple = $isMultiple;

        return $this;
    }

    /**
     * Get isMultiple
     *
     * @return boolean 
     */
    public function getIsMultiple()
    {
        return $this->isMultiple;
    }

    /**
     * Set multipleId
     *
     * @param integer $multipleId
     * @return Data
     */
    public function setMultipleId($multipleId)
    {
        $this->multipleId = $multipleId;

        return $this;
    }

    /**
     * Get multipleId
     *
     * @return integer 
     */
    public function getMultipleId()
    {
        return $this->multipleId;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Data
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
     * @return Data
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
     * @return Data
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
     * Set source
     *
     * @param \Sample\CsvBundle\Entity\Sources $source
     * @return Data
     */
    public function setSource(\Sample\CsvBundle\Entity\Sources $source = null)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return \Sample\CsvBundle\Entity\Sources 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set file
     *
     * @param \Sample\CsvBundle\Entity\Files $file
     * @return Data
     */
    public function setFile(\Sample\CsvBundle\Entity\Files $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \Sample\CsvBundle\Entity\Files 
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set period
     *
     * @param \Sample\CsvBundle\Entity\Periods $period
     * @return Data
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
     * Set multiple
     *
     * @param \Sample\CsvBundle\Entity\Multiple $multiple
     * @return Data
     */
    public function setMultiple(\Sample\CsvBundle\Entity\Multiple $multiple = null)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Get multiple
     *
     * @return \Sample\CsvBundle\Entity\Multiple 
     */
    public function getMultiple()
    {
        return $this->multiple;
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
