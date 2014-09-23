<?php

namespace Sample\CsvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Files
 */
class Files
{
    /**
     * FileTypeId = 1
     */
    public static $fileTypeCsv = 1;

    /**
     * FileTypeId = 2
     */
    public static $fileTypeMultiple = 2;
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $fileTypeId;

    /**
     * @var integer
     */
    private $sourceId;

    /**
     * @var integer
     */
    private $periodId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @var \Sample\CsvBundle\Entity\FileTypes
     */
    private $fileType;

    /**
     * @var \Sample\CsvBundle\Entity\Sources
     */
    private $source;

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
     * Set fileTypeId
     *
     * @param integer $fileTypeId
     * @return Files
     */
    public function setFileTypeId($fileTypeId)
    {
        $this->fileTypeId = $fileTypeId;

        return $this;
    }

    /**
     * Get fileTypeId
     *
     * @return integer 
     */
    public function getFileTypeId()
    {
        return $this->fileTypeId;
    }

    /**
     * Set sourceId
     *
     * @param integer $sourceId
     * @return Files
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
     * Set periodId
     *
     * @param integer $periodId
     * @return Files
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
     * Set name
     *
     * @param string $name
     * @return Files
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Files
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set delimiter
     *
     * @param string $delimiter
     * @return Files
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * Get delimiter
     *
     * @return string 
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Files
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Files
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
     * @return Files
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
     * Set fileType
     *
     * @param \Sample\CsvBundle\Entity\FileTypes $fileType
     * @return Files
     */
    public function setFileType(\Sample\CsvBundle\Entity\FileTypes $fileType = null)
    {
        $this->fileType = $fileType;

        return $this;
    }

    /**
     * Get fileType
     *
     * @return \Sample\CsvBundle\Entity\FileTypes 
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * Set source
     *
     * @param \Sample\CsvBundle\Entity\Sources $source
     * @return Files
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
     * Set period
     *
     * @param \Sample\CsvBundle\Entity\Periods $period
     * @return Files
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
