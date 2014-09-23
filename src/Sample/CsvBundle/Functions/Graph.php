<?php

namespace Sample\CsvBundle\Functions;

use Sample\CsvBundle\Entity\General;
use Sample\CsvBundle\Functions\Shared;

class Graph extends Shared
{
    protected $_entityManager;

    public function __construct($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Return Request Params for Graph
     *
     * @used
     * - GraphController::indexAction
     * - GraphController::dataAction
     * - GraphController::exportAction
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param boolean $post DEFAULT false
     * @return array
     */
    public function getRequestParams($request, $post = false)
    {
        $franchise = ($post) ? $request->request->get('franchise') : $request->query->get('f');
        $topicId   = ($post) ? $request->request->get('topicId')   : $request->query->get('t');
        $topicName = ($post) ? $request->request->get('topicName') : $request->query->get('n');

        return array(
            'franchise'  => json_decode($franchise, true),
            'topicId'    => json_decode($topicId, true),
            'topicNamee' => json_decode($topicName, true)
        );
    }

    /**
     * Return Path for Graph JS
     *
     * @used
     * - GraphController::indexAction
     * @param array $requestParams
     * @return string
     */
    public function getPath($requestParams)
    {
        $empty = '["'.General::$emptyField.'"]';

        // Franchise
        $path = '?f=' . (empty($requestParams['franchise'])) ? $empty : json_encode($requestParams['franchise']);

        // TopicId
        $path .= '&t=' . (empty($requestParams['topicId'])) ? $empty : json_encode($requestParams['topicId']);

        // TopicName
        $path .= '&n=' . (empty($requestParams['topicName'])) ? $empty : json_encode($requestParams['topicName']);

        // Interval
        $interval = $requestParams['periodInterval'];
        $path .= '&m1=' . $interval['start']['m'] . '&y1=' . $interval['start']['y'];
        $path .= '&m2=' . $interval['end']['m']   . '&y2=' . $interval['end']['y'];

        return $path;
    }

    /**
     * Return General Data
     *
     * @used
     * - GraphController::dataAction
     * @param array $requestParams
     * @param string $path
     * @return array
     */
    public function getGenerals($requestParams, $path)
    {
        return $this->_entityManager
            ->getRepository('SampleCsvBundle:General')
            ->getGenerals($requestParams, $path);
    }
}
