<?php

namespace Sample\CsvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SourceConfigurations
 */
class SourceConfigurations
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
    private $sourceConfigurationTypeId;

    /**
     * @var string
     */
    private $setting;

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
     * @var \Sample\CsvBundle\Entity\SourceConfigurationTypes
     */
    private $sourceConfigurationType;


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
     * @return SourceConfigurations
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
     * Set sourceConfigurationTypeId
     *
     * @param integer $sourceConfigurationTypeId
     * @return SourceConfigurations
     */
    public function setSourceConfigurationTypeId($sourceConfigurationTypeId)
    {
        $this->sourceConfigurationTypeId = $sourceConfigurationTypeId;

        return $this;
    }

    /**
     * Get sourceConfigurationTypeId
     *
     * @return integer 
     */
    public function getSourceConfigurationTypeId()
    {
        return $this->sourceConfigurationTypeId;
    }

    /**
     * Set setting
     *
     * @param string $setting
     * @return SourceConfigurations
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;

        return $this;
    }

    /**
     * Get setting
     *
     * @return string 
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return SourceConfigurations
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
     * @return SourceConfigurations
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
     * @return SourceConfigurations
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
     * Set sourceConfigurationType
     *
     * @param \Sample\CsvBundle\Entity\SourceConfigurationTypes $sourceConfigurationType
     * @return SourceConfigurations
     */
    public function setSourceConfigurationType(\Sample\CsvBundle\Entity\SourceConfigurationTypes $sourceConfigurationType = null)
    {
        $this->sourceConfigurationType = $sourceConfigurationType;

        return $this;
    }

    /**
     * Get sourceConfigurationType
     *
     * @return \Sample\CsvBundle\Entity\SourceConfigurationTypes 
     */
    public function getSourceConfigurationType()
    {
        return $this->sourceConfigurationType;
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
