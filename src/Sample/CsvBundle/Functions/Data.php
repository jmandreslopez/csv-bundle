<?php

namespace Sample\CsvBundle\Functions;

use Sample\CsvBundle\Functions\Shared;

class Data extends Shared
{
    protected $_entityManager;

    public function __construct($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Return Request Params for Data
     *
     * @used
     * - DataController::indexAction
     * - DataController::dataAction
     * - DataController::exportAction
     * @param array $request
     * @param boolean $post = false
     * @return array
     */
    public function getRequestParams($request, $post = false)
    {
        return array(
            'sourceId' => ($post) ? $request->request->get('sourceId') : $request->query->get('s'),
            'periodId' => ($post) ? $request->request->get('periodId') : $request->query->get('p'),
            'topicId'  => ($post) ? $request->request->get('topicId')  : $request->query->get('t')
        );
    }

    /**
     * Return Headers
     *
     * @used
     * - DataController::dataAction
     * - DataController::exportAction
     * @param array $requestParams
     * @return array
     */
    public function getHeaders($requestParams)
    {
        return $this->_entityManager
            ->getRepository('SampleCsvBundle:Data')
            ->getHeaders($requestParams);
    }
}