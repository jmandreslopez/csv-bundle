<?php

namespace Sample\CsvBundle\Functions;

use Sample\CsvBundle\Functions\Shared;

class Multiple extends Shared
{
    protected $_entityManager;

    public function __construct($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Return Request Params for Multiple
     *
     * @used
     * - MultipleController::configurationAction
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function getRequestParams($request)
    {
        return array(
            'multipleId' => $request->query->get('m'),
            'topicId'    => $request->query->get('t'),
            'percentage' => $request->query->get('pg')
        );
    }
}