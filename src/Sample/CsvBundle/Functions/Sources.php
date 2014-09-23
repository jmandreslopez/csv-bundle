<?php

namespace Sample\CsvBundle\Functions;

use Sample\CsvBundle\Functions\Shared;

class Sources extends Shared
{
    protected $_entityManager;

    public function __construct($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Return Request Params for Sources
     *
     * @used
     * - SourcesController::configurationAction
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function getRequestParams($request)
    {
        return array(
            'sourceId' => $request->query->get('s'),
            'topicId'  => $request->query->get('t'),
            'prices'   => $request->query->get('p')
        );
    }

    /**
     * Return Source Configurations
     *
     * @used
     * - SourceController::indexAction
     * @param integer $sourceId
     * @return objects array
     */
    public function getSourceConfigurations($sourceId)
    {
        return $this->_entityManager
            ->getRepository('SampleCsvBundle:SourceConfigurations')
            ->findBy(array(
                'sourceId' => $sourceId
            ));
    }
}