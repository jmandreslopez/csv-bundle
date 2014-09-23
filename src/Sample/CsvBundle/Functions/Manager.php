<?php

namespace Sample\CsvBundle\Functions;

use Sample\CsvBundle\Functions\Shared;

class Manager extends Shared
{
    protected $_entityManager;

    public function __construct($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Return Pagination Query
     *
     * @used
     * - ManagerController::indexAction
     * @return query
     */
    public function getPaginationQuery()
    {
        return $this->_entityManager
            ->getRepository('SampleCsvBundle:Files')
            ->getPaginationQuery();
    }
}
