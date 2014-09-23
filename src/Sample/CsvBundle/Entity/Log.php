<?php

namespace Sample\CsvBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 */
class Log
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $logTypeId;

    /**
     * @var string
     */
    private $process;

    /**
     * @var string
     */
    private $description;

    /**
     * @var boolean
     */
    private $fixed;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * @var \Sample\CsvBundle\Entity\LogTypes
     */
    private $logType;


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
     * Set logTypeId
     *
     * @param integer $logTypeId
     * @return Log
     */
    public function setLogTypeId($logTypeId)
    {
        $this->logTypeId = $logTypeId;

        return $this;
    }

    /**
     * Get logTypeId
     *
     * @return integer 
     */
    public function getLogTypeId()
    {
        return $this->logTypeId;
    }

    /**
     * Set process
     *
     * @param string $process
     * @return Log
     */
    public function setProcess($process)
    {
        $this->process = $process;

        return $this;
    }

    /**
     * Get process
     *
     * @return string 
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Log
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set fixed
     *
     * @param boolean $fixed
     * @return Log
     */
    public function setFixed($fixed)
    {
        $this->fixed = $fixed;

        return $this;
    }

    /**
     * Get fixed
     *
     * @return boolean 
     */
    public function getFixed()
    {
        return $this->fixed;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Log
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
     * @return Log
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
     * Set logType
     *
     * @param \Sample\CsvBundle\Entity\LogTypes $logType
     * @return Log
     */
    public function setLogType(\Sample\CsvBundle\Entity\LogTypes $logType = null)
    {
        $this->logType = $logType;

        return $this;
    }

    /**
     * Get logType
     *
     * @return \Sample\CsvBundle\Entity\LogTypes 
     */
    public function getLogType()
    {
        return $this->logType;
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
