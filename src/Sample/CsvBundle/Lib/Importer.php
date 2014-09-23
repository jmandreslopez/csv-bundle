<?php

namespace Sample\CsvBundle\Lib;

use Sample\CsvBundle\Entity\Data;
use Avro\CsvBundle\Import\Importer as BaseImporter;

class Importer extends BaseImporter
{
    private $_debug = false;
    private $_multiple = array();

    /**
     * Import the CSV and persist to database
     *
     * @used
     * - ImportCommand::_importMultiple
     * @param \Sample\CsvBundle\Entity\Sources $source
     * @param \Sample\CsvBundle\Entity\Files $file
     * @param \Sample\CsvBundle\Entity\Periods $period
     * @param objects array $configurations
     * @return boolean
     */
    public function importMultiple($source, $file, $period, $configurations)
    {
        // DEBUG
        $this->_debug('[Multiple Importing Process Initiated]');

        $config = array();
        foreach ($configurations as $configuration) 
        {
            switch ($configuration->getName())
            {
                case 'multiple_id':     $config['multiple_id'] = $configuration->getValue();
                                        break;

                case 'topic_id':        $config['topic_id'] = $configuration->getValue();
                                        break;

                case 'percentage':      $config['percentage'] = $configuration->getValue();
                                        break;

                default:                break;
            }
        }

        $this->_importLoop('Multiple', $source, $file, $period, $config);

        // DEBUG
        $this->_debug('[Multiple Importing Process Finished]');

        return true;
    }

    /**
     * Import the CSV and persist to database
     *
     * @used
     * - ImportCommand::_importCsv
     * @param \Sample\CsvBundle\Entity\Sources $source
     * @param \Sample\CsvBundle\Entity\Files $file
     * @param \Sample\CsvBundle\Entity\Periods $period
     * @param objects array $configurations
     * @return boolean
     */
    public function importCsv($source, $file, $period, $configurations)
    {
        // DEBUG
        $this->_debug('[Importing Process Initiated]');

        $config = array('topicId' => '', 'price' => 0);
        foreach ($configurations as $configuration) 
        {
            switch($configuration->getSourceConfigurationTypeId())
            {
                // Topic ID
                case '1':       $config['topicId'] = $configuration->getSetting();
                                break;

                // Price
                case '2':       $config['price'] = $configuration->getSetting();
                                break;

                default:        break;
            }
        }

        $this->_importLoop('Csv', $source, $file, $period, $config);

        // DEBUG
        $this->_debug('[Importing Process Finished]');

        return true;
    }

    /**
     * [PRIVATE]
     * Get Files Headers
     *
     * @used
     * - self::_importLoop
     * @return array
     */
    private function _getHeaders()
    {
        $fileHeaders = $this->reader->getHeaders();

        $headers = array();
        foreach ($fileHeaders as $fileHeader) {
            $headers[] = trim($fileHeader);
        }

        return $headers;
    }

    /**
     * [PRIVATE]
     * Import Loop for both
     * Multiple & CSV
     *
     * @used
     * - self::importMultiple
     * - self::importCsv
     * @param string $type
     * @param \Sample\CsvBundle\Entity\Sources $source
     * @param \Sample\CsvBundle\Entity\Files $file
     * @param \Sample\CsvBundle\Entity\Periods $period
     * @param array $config
     * @return boolean
     */
    private function _importLoop($type, $source, $file, $period, $config)
    {
        $this->objectManager->getConnection()->getConfiguration()->setSQLLogger(null);

        $this->importCount = 0;
        while ($row = $this->reader->getRow())
        {
            switch ($type)
            {
                case 'Csv':         $this->_addCsvRow($row, $this->_getHeaders(), $source, $file, $period, $config);
                                    break;

                case 'Multiple':    $this->_addMultipleRow($row, $this->_getHeaders(), $source, $file, $period, $config);
                                    break;

                default:            break;
            }
            $this->importCount++;

            if (($this->importCount % $this->batchSize) == 0)
            {
                $this->objectManager->flush();
                $this->objectManager->clear($this->class);

                // DEBUG
                $this->_debug('*', false); // Progress
            }
        }

        // DEBUG
        $this->_debug(''); // Line break

        $this->objectManager->flush();
        $this->objectManager->clear($this->class);

        return true;
    }

    /**
     * [PRIVATE]
     * Import Multiple Row
     *
     * @used
     * - self::_importLoop
     * @param array $row
     * @param array $headers
     * @param \Sample\CsvBundle\Entity\Sources $source
     * @param \Sample\CsvBundle\Entity\Files $file
     * @param \Sample\CsvBundle\Entity\Periods $period
     * @param array $config
     */
    private function _addMultipleRow($row, $headers, $source, $file, $period, $config)
    {
        // Create new entity
        $entity = $this->_setMain(new $this->class(), $source, $file, $period);

        // MultipleId
        $entity->setMultipleId('-No Multiple Id-');

        // TopicId
        $entity->setTopicId('-No Topic ID-');

        // Percentage
        $entity->setPercentage(0);

        // Headers
        foreach ($headers as $key => $header)
        {
            switch ($header)
            {
                case $config['multiple_id']:    $entity->setMultipleId($row[$key]);
                                                break;

                case $config['topic_id']:       $entity->setTopicId($row[$key]);
                                                break;

                case $config['percentage']:     $entity->setPercentage($this->_getPercentage($row[$key]));
                                                break;

                default:                        break;
            }
        }

        // IsActive
        $entity->setIsActive(true);

        $this->objectManager->persist($entity);
    }

    /**
     * [PRIVATE]
     * Import CSV Row
     *
     * @used
     * - self::_importLoop
     * @param array $row
     * @param array $headers
     * @param \Sample\CsvBundle\Entity\Sources $source
     * @param \Sample\CsvBundle\Entity\Files $file
     * @param \Sample\CsvBundle\Entity\Periods $period
     * @param array $config
     */
    private function _addCsvRow($row, $headers, $source, $file, $period, $config)
    {
        // Create new entity
        $entity = $this->_setMain(new $this->class(), $source, $file, $period);

        // TopicId
        $entity->setTopicId('-No Topic ID-');

        // Price
        $entity->setPrice(0);

        // Headers/Values
        $extraHeaders = array();
        $extraValues = array();
        foreach ($headers as $key => $header)
        {
            if ($header == $config['topicId']) {

                $entity->setTopicId($row[$key]);

            } elseif ($header == $config['price']) {

                $entity->setPrice($this->_getPrice($row[$key]));

            } else {

                $extraHeaders[] = $header;
                $extraValues[] = $row[$key];
            }
        }

        // Extra Headers
        $entity->setExtraHeaders(json_encode($extraHeaders));

        // Extra Values
        $entity->setExtraValues(json_encode($extraValues));

        // Is Multiple
        if (in_array($entity->getTopicId(), $this->_multiple)) {
            $entity->setIsMultiple(true);
        } else {
            $entity->setIsMultiple(false);
        }

        // Persist
        $this->objectManager->persist($entity);

        // Multiple
        $this->_multiple($entity);
    }

    /**
     * [PRIVATE]
     * Return cleaned Percentage
     *
     * @used
     * - self::_addMultipleRow
     * @param string $percentage
     * @return string
     */
    private function _getPercentage($percentage)
    {
        $percentageFlag = false;
        if(strpos($percentage, '%')) {
            $percentageFlag = true;
        }

        // Blank spaces
        $percentage = trim($percentage);

        // $
        $percentage = str_replace("$", "", $percentage);

        // Comma
        $percentage = str_replace(",", "", $percentage);

        // %
        $percentage = str_replace("%", "", $percentage);

        // Parentheses
        if (preg_match("/\((\d+(\.\d)?)\)/", $percentage)) {
            $percentage = str_replace("(", "", $percentage);
            $percentage = str_replace(")", "", $percentage);
            $percentage = $percentage * (-1);
        }

        // Empty
        if ($percentage == '' || $percentage == NULL || $percentage == '-') {
            $percentage = 0;
        }

        return ($percentageFlag) ? $percentage/100 : $percentage;
    }

    /**
     * [PRIVATE]
     * Set the default values for
     * both CSV & Multiple
     *
     * @used
     * - self::_addMultipleRow
     * - self::_addCsvRow
     * @param \Sample\CsvBundle\Entity\Data $entity
     * @param \Sample\CsvBundle\Entity\Sources $source
     * @param \Sample\CsvBundle\Entity\Files $file
     * @param \Sample\CsvBundle\Entity\Periods $period
     * @return \Sample\CsvBundle\Entity\Data
     */
    private function _setMain($entity, $source, $file, $period)
    {
        // Source
        $entity->setSourceId($source->getId());
        $entity->setSource($source);

        // File
        $entity->setFileId($file->getId());
        $entity->setFile($file);

        // Period
        $entity->setPeriodId($period->getId());
        $entity->setPeriod($period);

        // IsActive
        $entity->setIsActive(true);

        return $entity;
    }

    /**
     * [PRIVATE]
     * Return cleaned Cost
     *
     * @used
     * - self::_addCsvRow
     * @param type $cost
     * @return int
     */
    private function _getCost($cost)
    {
        // Blank spaces
        $cost = trim($cost);

        // $
        $cost = str_replace("$", "", $cost);

        // Comma
        $cost = str_replace(",", "", $cost);

        // Parentheses
        if (preg_match('!\(([^\)]+)\)!', $cost)) {
            $cost = str_replace("(", "", $cost);
            $cost = str_replace(")", "", $cost);
            $cost = $cost * (-1);
        }

        // Empty
        if ($cost == '' || $cost == NULL || $cost == '-') {
            $cost = 0;
        }

        return $cost;
    }

    /**
     * [PRIVATE]
     * Process Multiple
     *
     * @used
     * - self::_addCsvRow
     * @param \Sample\CsvBundle\Entity\Data $entity
     */
    private function _multiple($entity)
    {
        if ($entity->getIsMultiple())
        {
            $multiple = $this->objectManager
                ->getRepository('SampleCsvBundle:Multiple')
                ->findBy(array(
                    'sourceId'   => $entity->getSource()->getId(),
                    'periodId'   => $entity->getPeriod()->getId(),
                    'multipleId' => $entity->getTopicId(),
                    'isActive'   => true
                ));

            foreach ($multiple as $multi)
            {
                // Data
                $data = new Data();

                // Properties
                $data->setSourceId($entity->getSource()->getId());
                $data->setFileId($entity->getFile()->getId());
                $data->setPeriodId($entity->getPeriod()->getId());
                $data->setTopicId($multi->getTopicId());
                $data->setPrice($this->_getMultiplePercentage($entity->getPrice(), $multi->getPercentage()));
                $data->setExtraHeaders($entity->getExtraHeaders());
                $data->setExtraValues($entity->getExtraValues());
                $data->setIsMultiple(false);
                $data->setMultipleId($multi->getId());
                $data->setIsActive(true);

                // Relationships
                $data->setSource($entity->getSource());
                $data->setFile($entity->getFile());
                $data->setPeriod($entity->getPeriod());
                $data->setMultiple($multi);

                $this->objectManager->persist($data);
            }

            // Disable Multiple
            $entity->setIsActive(false);

            $this->objectManager->persist($entity);
            $this->objectManager->flush();
        }
    }

    /**
     * [PRIVATE]
     * Return calculated Multiple Percentage
     *
     * @used
     * - self::_multiple
     * @param float $value
     * @param float $percentage
     * @return float
     */
    private function _getMultiplePercentage($value, $percentage)
    {
        return $percentage*$value;
    }

    /**
     * Set Multiple for CSV
     *
     * @used
     * - ImportCommand::_importCsv
     * @param array $multiple
     */
    public function setMultiple($multiple)
    {
        $this->_multiple = $multiple;
    }

    /**
     * Set Debug
     *
     * @used
     * - ImportCommand::_importMultiple
     * - ImportCommand::_importCsv
     * @param boolean $debug
     */
    public function setDebug($debug)
    {
        $this->_debug = $debug;
    }

    /**
     * [PRIVATE]
     * Debug
     *
     * @used
     * - self::importMultiple
     * - self::importCsv
     * - self::_importLoop
     * @param string $text
     * @param boolean $break DEFAULT true
     */
    private function _debug($text, $break = true)
    {
        if ($this->_debug) {
            echo $text;
            if ($break) {
                echo PHP_EOL;
            }
        }
    }
}