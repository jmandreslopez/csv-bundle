<?php

namespace Sample\CsvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * General
 */
class General
{
    public static $nameNotAvailable = 'NOT AVAILABLE';
    public static $emptyField = 'EMPTY';
    public static $allField = 'ALL';
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $sourceId;

    /**
     * @var string
     */
    private $topicId;

    /**
     * @var string
     */
    private $topicName;

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
     * @return General
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
     * Set topicId
     *
     * @param string $topicId
     * @return General
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
     * Set topicName
     *
     * @param string $topicName
     * @return General
     */
    public function setTopicName($topicName)
    {
        $this->topicName = $topicName;

        return $this;
    }

    /**
     * Get topicName
     *
     * @return string 
     */
    public function getTopicName()
    {
        return $this->topicName;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return General
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
     * @return General
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
     * @return General
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
     * @return General
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
