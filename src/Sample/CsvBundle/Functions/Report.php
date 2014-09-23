<?php

namespace Sample\CsvBundle\Functions;

use Sample\CsvBundle\Functions\Shared;

class Report extends Shared
{
    protected $_entityManager;

    public function __construct($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Return Request Params for Report
     *
     * @used
     * - ReportController::indexAction
     * - ReportController::dataAction
     * - ReportController::exportAction
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param boolean $post DEFAULT false
     * @param boolean $period DEFAULT false
     * @return array
     */
    public function getRequestParams($request, $post = false, $period = false)
    {
        $sourceId  = ($post) ? $request->request->get('sourceId')  : $request->query->get('s');
        $franchise = ($post) ? $request->request->get('franchise') : $request->query->get('f');
        $topicId   = ($post) ? $request->request->get('topicId')   : $request->query->get('t');
        $topicName = ($post) ? $request->request->get('topicName') : $request->query->get('n');

        $requestParams = array(
            'sourceId'    => json_decode($sourceId, true),
            'franchise'   => json_decode($franchise, true),
            'topicId'   => json_decode($topicId, true),
            'topicName' => json_decode($topicName, true)
        );

        if ($period) {
            $periods = $request->request->get('periods');
            $requestParams['periods'] = json_decode($periods, true);
        }

        return $requestParams;
    }

    /**
     * Return Headers
     *
     * @used
     * - ReportController::dataAction
     * - ReportController::exportAction
     * @param array $requestParams
     * @return array
     */
    public function getHeaders($requestParams)
    {
        return $this->_entityManager
            ->getRepository('SampleCsvBundle:Data')
            ->getReportHeaders($requestParams);
    }
}