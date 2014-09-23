<?php

namespace Sample\CsvBundle\EventListener;

use Oneup\UploaderBundle\Event\PostPersistEvent;
use Sample\CsvBundle\Entity\Files;

class UploadListener
{
    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Create the File record on Upload
     *
     * @used
     * - UploadController
     * @param \Oneup\UploaderBundle\Event\PostPersistEvent $event
     */
    public function onUpload(PostPersistEvent $event)
    {
        $file = $event->getFile();
        $request = $event->getRequest();

        $this->_createRecord($file, $request);

        // Upload Response
        $response = $event->getResponse();
        $response['files'] = array(
            'name' => $file->getName(),
            'size' => $file->getSize(),
            'deleteType' => 'DELETE'
        );
    }

    /**
     * [PRIVATE]
     * Create new Source File
     *
     * @used
     * - self::onUpload
     * @param \Sample\CsvBundle\Entity\Files $file
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    private function _createRecord($file, $request)
    {
        $name      = $file->getName();
        $path      = $file->getPath();
        $type      = $request->request->get('type');
        $month     = $request->request->get('month');
        $year      = $request->request->get('year');
        $delimiter = $request->request->get('delimiter');
        $sourceId  = $request->request->get('source');

        // Period
        $period = $this->entityManager
            ->getRepository('SampleCsvBundle:Periods')
            ->getPeriod($month, $year);

        // Source
        $source = $this->entityManager
            ->getRepository('SampleCsvBundle:Sources')
            ->find($sourceId);

        // File Type
        $fileType = $this->entityManager
            ->getRepository('SampleCsvBundle:FileTypes')
            ->find($type);

        if (!empty($source) && !empty($fileType))
        {
            $file = new Files();
            $file->setFileTypeId($fileType->getId());
            $file->setSourceId($source->getId());
            $file->setPeriodId($period->getId());
            $file->setName($name);
            $file->setPath($path);
            $file->setDelimiter($delimiter);
            $file->setStatus('pending');
            $file->setFileType($fileType);
            $file->setSource($source);
            $file->setPeriod($period);

            $this->entityManager->persist($file);
            $this->entityManager->flush();
        }
    }
}
