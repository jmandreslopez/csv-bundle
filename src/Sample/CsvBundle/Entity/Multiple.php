<?php

namespace Sample\CsvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Multiple
 */
class Multiple
{
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
    private $multipleId;

    /**
     * @var string
     */
    private $topicId;

    /**
     * @var float
     */
    private $percentage;

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
     * @return Multiple
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
     * @return Multiple
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
     * @return Multiple
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
     * Set multipleId
     *
     * @param string $multipleId
     * @return Multiple
     */
    public function setMultipleId($multipleId)
    {
        $this->multipleId = $multipleId;

        return $this;
    }

    /**
     * Get multipleId
     *
     * @return string 
     */
    public function getMultipleId()
    {
        return $this->multipleId;
    }

    /**
     * Set topicId
     *
     * @param string $topicId
     * @return Multiple
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
     * Set percentage
     *
     * @param float $percentage
     * @return Multiple
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Get percentage
     *
     * @return float 
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Multiple
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
     * @return Multiple
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
     * @return Multiple
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
     * @return Multiple
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
     * @return Multiple
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
     * @return Multiple
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
