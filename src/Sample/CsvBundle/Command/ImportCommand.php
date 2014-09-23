<?php

namespace Sample\CsvBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Doctrine\Common\Collections;
use Sample\CsvBundle\Entity\Files;

class ImportCommand extends ContainerAwareCommand
{
    private $_debug = false;
    private $_entityManager;
    private $_importer;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('csv:import')
            ->setDescription('Import CSV Files')
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                'If set, it will turn the DEBUG on'
            );
    }

    /**
     * Execute command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '-1');

        if ($input->getOption('debug')) {
            $this->_debug = true;
        }

        echo '[Import Process: Started]'.PHP_EOL;
        
        // Default Objects
        $this->_entityManager = $this->getContainer()
            ->get('doctrine.orm.entity_manager');

        // Importer
        $this->_importer = $this->getContainer()
            ->get('csv.importer');

        // Remove previous files
        $this->_removeFiles();

        // Import Multiple
        $this->_importMultiple();

        // Import CSV
        $this->_importCsv();

        echo '[Import Process: Finished]'.PHP_EOL;
    }

    /**
     * [PRIVATE]
     * Remove previous files
     *
     * @used
     * - self::execute
     */
    private function _removeFiles()
    {
        $files = $this->_entityManager
            ->getRepository('SampleCsvBundle:Files')
            ->getNotPending();

        foreach ($files as $file)
        {
            // File Path
            $path = $file->getPath().'/'.$file->getFilename();

            // Remove File
            $this->_remove($path, false);
        }
    }

    /**
     * [PRIVATE]
     * Import Multiple
     *
     * @used
     * - self::execute
     */
    private function _importMultiple()
    {
        // Files: Multiple
        $multipleFiles = $this->_entityManager
            ->getRepository('SampleCsvBundle:Files')
            ->getMultiple();

        if (!empty($multipleFiles))
        {
            // DEBUG
            $this->_debug('[Multiple Process Started]');

            // Lock files
            $this->_lockFiles($multipleFiles);

            // DEBUG
            $this->_debug('[Splits Files Locked]');

            foreach ($multipleFiles as $multipleFile)
            {
                // Multiple Configurations
                $configurations = $this->_entityManager
                    ->getRepository('SampleCsvBundle:MultipleConfigurations')
                    ->findAll();

                if (empty($configurations)) {

                    // ERROR: No configurations
                    $this->_log(1, '{Info: No multiple configuration}', true);

                } else {
                    
                    try {
                        
                        // Source Object
                        $source = $multipleFile->getSource();

                        // Period Object
                        $period = $multipleFile->getPeriod();

                        // File Path
                        $path = $multipleFile->getPath().'/'.$multipleFile->getName();
                        
                        // DEBUG
                        $this->_debug('[Processing Multiple File: '.$multipleFile->getName().']');
                        $this->_debug('[Source: '.$source->getName().']');
                        $this->_debug('[Period: '.$period->getLabel().']');

                        // Importer
                        $this->_importer->init($path, 'Sample\CsvBundle\Entity\Multiple', $multipleFile->getDelimiter());

                        // DEBUG on Importer
                        $this->_importer->setDebug($this->_debug);

                        // Import
                        $this->_importer->importSplit($source, $multipleFile, $period, $configurations);

                        // Set File Status: Waiting acknowledge
                        $this->_statusFile($multipleFile, 'done');

                        // DEBUG
                        $this->_debug('[File Status: Done]');

                        // Remove File
                        $this->_remove($path, true);

                    } catch (\Doctrine\ORM\ORMException $e) {

                        // ERROR
                        echo '[ORMException]: '.$e->getMessage().PHP_EOL;

                    } catch (\Doctrine\DBAL\DBALException $e) {

                        // ERROR
                        echo '[DBALException]: '.$e->getMessage().PHP_EOL;

                    } catch (\Exception $e) {

                        // ERROR
                        echo '[Exception]: '.$e->getMessage().PHP_EOL;
                    }
                }
            }

            // Flush all remaining
            $this->_entityManager->flush();

            // DEBUG
            $this->_debug('[Multiple Import Process Ended]');

        } else {

            // DEBUG
            $this->_debug('[No multiple files to import]');
        }
    }

    /**
     * [PRIVATE]
     * Import CSV
     *
     * @used
     * - self::execute
     */
    private function _importCsv()
    {
        // Files: CSV
        $csvFiles = $this->_entityManager
            ->getRepository('SampleCsvBundle:Files')
            ->getCsv();

        if (!empty($csvFiles))
        {
            // DEBUG
            $this->_debug('[CSV Process Started]');

            // Lock files
            $this->_lockFiles($csvFiles);

            // DEBUG
            $this->_debug('[Files Locked]');

            // Import each file
            foreach($csvFiles as $csvFile)
            {
                // Source Object
                $source = $csvFile->getSource();                

                // Source Configurations
                $configurations = $this->_entityManager
                    ->getRepository('SampleCsvBundle:SourceConfigurations')
                    ->getConfigurations($source->getId());

                if (empty($configurations)) {

                    // ERROR: No configurations
                    $this->_log(1, '{Info: No configurations, SourceId: '.$source->getId().'}', true);

                } else if (count($configurations) < 2) {

                    // ERROR: Missing one configuration
                    $this->_log(1, '{Info: Missing one configuration, SourceId: '.$source->getId().'}', true);

                } else {
                    
                    try {

                        // Period Object
                        $period = $csvFile->getPeriod();

                        // File Path
                        $path = $csvFile->getPath().'/'.$csvFile->getName();
                                               
                        // DEBUG
                        $this->_debug('[Processing File: '.$csvFile->getName().']');
                        $this->_debug('[Source: '.$source->getName().']');
                        $this->_debug('[Period: '.$period->getLabel().']');

                        // Importer
                        $this->_importer->init($path, 'Sample\CsvBundle\Entity\Data', $csvFile->getDelimiter());

                        // Multiple
                        $multiple = $this->_entityManager
                            ->getRepository('SampleCsvBundle:Multiple')
                            ->getMultiple($period->getId());

                        $this->_importer->setMultiple($multiple);

                        // DEBUG on Importer
                        $this->_importer->setDebug($this->_debug);

                        // Import
                        $this->_importer->importCsv($source, $csvFile, $period, $configurations);

                        // Set File Status: Waiting acknowledge
                        $this->_statusFile($csvFile, 'acknowledge');

                        // DEBUG
                        $this->_debug('[File Status: Waiting Acknowledge]');

                        // Setting Status to 'Finish'
                        $this->_entityManager
                            ->getRepository('SampleCsvBundle:Prices')
                            ->setFinishPrice();

                        // DEBUG
                        $this->_debug('[Status set to: Finish]');

                        // Remove File
                        $this->_remove($path, true);

                    } catch (\Doctrine\ORM\ORMException $e) {

                        // ERROR
                        echo '[ORMException]: '.$e->getMessage().PHP_EOL;

                    } catch (\Doctrine\DBAL\DBALException $e) {

                        // ERROR
                        echo '[DBALException]: '.$e->getMessage().PHP_EOL;

                    } catch (\Exception $e) {

                        // ERROR
                        echo '[Exception]: '.$e->getMessage().PHP_EOL;
                    }
                }
            }

            // Flush all remaining
            $this->_entityManager->flush();

            // DEBUG
            $this->_debug('[Import Process Ended]');

        } else {

            // DEBUG
            $this->_debug('[Nothing to import]');
        }
    }

    /**
     * [PRIVATE]
     * Lock file before Import
     *
     * @used
     * - self::_importMultiple
     * - self::_importCsv
     * @param array $files
     */
    private function _lockFiles($files)
    {
        foreach($files as $file)
        {
            $file->setStatus('processing');
            $this->_entityManager->persist($file);
        }
        $this->_entityManager->flush();
    }

    /**
     * [PRIVATE]
     * Change File Status
     *
     * @used
     * - self::_importMultiple
     * - self::_importCsv
     * @param object $file
     * @param string $status
     */
    private function _statusFile($file, $status)
    {
        $file->setStatus($status);
        $this->_entityManager->persist($file);
    }

    /**
     * [PRIVATE]
     * Remove file
     *
     * @used
     * - self::_importMultiple
     * - self::_importCsv
     * @param string $path
     * @param boolean $debug
     */
    private function _remove($path, $debug)
    {
        $fileSystem = new Filesystem();
        
        try {
            
            // Remove file
            if ($fileSystem->exists($path)) {
                $fileSystem->remove($path);
            }
            
        } catch (IOExceptionInterface $e) {
            
            // ERROR
            echo '[IOExceptionInterface]: '.$e->getMessage().PHP_EOL;
            
        }

        // DEBUG
        if ($debug) {
            $this->_debug('[Remove File: Done]');
        }
    }

    /**
     * [PRIVATE]
     * Log
     *
     * @used
     * - self::_importMultiple
     * - self::_importCsv
     * @param integer $logType
     * @param string $description
     * @param boolean $error
     */
    private function _log($logType, $description, $error = false)
    {
        $this->_entityManager
            ->getRepository('SampleCsvBundle:Log')
            ->log($logType, 'ImportCommand', $description);

        if ($error) {

            // DEBUG
            $this->_debug('[ERROR]');

            exit();
        }
    }

    /**
     * [PRIVATE]
     * Debug
     *
     * @used
     * - self::execute
     * - self::_importMultiple
     * - self::_importCsv
     * @param string $text
     * @param boolean $break
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