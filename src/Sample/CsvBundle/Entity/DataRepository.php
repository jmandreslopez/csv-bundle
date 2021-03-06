<?php

namespace Sample\CsvBundle\Entity;

use Sample\CsvBundle\Entity\SharedRepository;
use Sample\CsvBundle\Entity\Data;
use Doctrine\ORM\Query\Expr\Orx;

/**
 * DataRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DataRepository extends SharedRepository
{
    private $_dataTableParams = array();

    /**
     * [PRIVATE]
     * Get Query Builder for Data
     *
     * @used
     * - getResults
     * - getExportData
     * @param array $requestParams
     * @param string $type
     * @return QueryBuilder
     */
    private function _getBuilder($requestParams, $type)
    {
        $builder = $this->getEntityManager()
            ->createQueryBuilder('d')
            ->from($this->getEntityName(), 'd')
            ->leftJoin('d.multiple', 'm')
            ->where('d.sourceId = :sourceId')
            ->andWhere('d.periodId = :periodId')
            ->andWhere('d.topicId = :topicId')
            ->andWhere('d.isActive = :isActive')
            ->setParameters(array(
                'sourceId' => $requestParams['sourceId'],
                'periodId' => $requestParams['periodId'],
                'topicId'  => $requestParams['topicId'],
                'isActive' => true
            ));

        switch ($type) 
        {
            case 'data':        $builder->select('d.topicId, d.price, d.headers, d.values, m.multipleId');
                                break;

            case 'count':       $builder->select('COUNT(d)')->setMaxResults(1);
                                break;

            case 'headers':     $builder->select('DISTINCT(d.headers) AS headers')->orderBy('d.sourceId');
                                break;

            default:            break;
        }

        return $builder;
    }

    /**
     * [PRIVATE]
     * Get Query Builder for Report
     *
     * @used
     * - self::getReportResults
     * - self::getReportExportData
     * @param array $requestParams
     * @param string $type
     * @return QueryBuilder
     */
    private function _getReportBuilder($requestParams, $type)
    {
        $builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->from($this->getEntityName(), 'd')
            ->leftJoin('d.multiple', 'm')
            ->where('d.sourceId = :sourceId')
            ->andWhere('d.isActive = :isActive')            
            ->setParameters(array(
                'sourceId' => $requestParams['sourceId'],
                'isActive' => true
            ));

        // Periods
        $periods = array_values($requestParams['periods']);
        if (!empty($requestParams['periods'])) {
            $builder->andWhere('d.periodId IN (:periods)')
                ->setParameter('periods', $periods);
        }

        // Topic Ids
        $topicIds = $this->_getTopicIds($requestParams);
        if (!empty($topicIds)) {
            $builder->andWhere('d.topicId IN (:topicIds)')
                ->setParameter('topicIds', $topicIds);
        }

        switch ($type) 
        {
            case 'data':        $builder->select('g.topicName, d.topicId, d.price, d.headers, d.values, m.multipleId')
                                    ->leftJoin(
                                        'SampleCsvBundle:General', 'g',
                                        \Doctrine\ORM\Query\Expr\Join::WITH,
                                        'd.topicId = g.topicId AND d.sourceId = g.sourceId'
                                    );
                                break;

            case 'count':       $builder->select('COUNT(d)')->setMaxResults(1);
                                break;

            case 'headers':     $builder->select('DISTINCT(d.extraHeaders) AS headers')->orderBy('d.sourceId');
                                break;

            default:            break;
        }

        return $builder;
    }

    /**
     * [PRIVATE]
     * Add Pagination to Builder
     *
     * @used
     * - self::getResults
     * - self::getReportResults
     * - self::getExportData
     * - self::getReportExportData
     * @param QueryBuilder $builder
     * @return QueryBuilder
     */
    private function _addPaginationToBuilder($builder)
    {
        if (isset($this->_dataTableParams['start']) && $this->_dataTableParams['length'] !== '-1') {
            return $builder
                ->setFirstResult((int)$this->_dataTableParams['start'])
                ->setMaxResults((int)$this->_dataTableParams['length']);
        }

        return $builder;
    }

    /**
     * [PRIVATE]
     * Add Ordering to Builder
     *
     * @used
     * - self::getResults
     * - self::getReportResults
     * - self::getExportData
     * - self::getReportExportData
     * @param QueryBuilder $builder
     * @return QueryBuilder
     */
    private function _addOrderingToBuilder($builder)
    {
        if (isset($this->_dataTableParams['order']) && !empty($this->_dataTableParams['order']))
        {
            $order = $this->_dataTableParams['order'][0];
            $order['dir'] = (isset($order['dir'])) ? $order['dir'] : 'ASC';
            $builder->orderBy('d.price', $order['dir']);
        }

        return $builder;
    }

    /**
     * [PRIVATE]
     * Add Filtering to Builder
     *
     * @used
     * - self::getResults
     * - self::getReportResults
     * - self::getExportData
     * - self::getReportExportData
     * @param QueryBuilder $builder
     * @return QueryBuilder
     */
    private function _addFilteringToBuilder($builder)
    {
        if (isset($this->_dataTableParams['search']) && !empty($this->_dataTableParams['search']['value']))
        {
            $search = $this->_dataTableParams['search'];

            $aLike = array();
            $aLike[] = $builder->expr()->like('d.topicId', ':search');
            $aLike[] = $builder->expr()->like('d.price', ':search');
            $aLike[] = $builder->expr()->like('d.values', ':search');

            $builder
                ->andWhere(new Orx($aLike))
                ->setParameter('search', '%'.$search['value'].'%');
        }

        return $builder;
    }

    /**
     * [PRIVATE]
     * Return Default Headers for Data & Report
     *
     * @used
     * - self::getHeaders
     * - self::getReportHeaders
     * @param array $dataResults
     * @return array
     */
    private function _getColumnHeaders($dataResults)
    {
        // Default Columns
        $headers = array();
        $headers[] = 'Topic Name';
        $headers[] = 'Topic ID';
        $headers[] = 'Price';
        $headers[] = Data::$multipleHeader;

        foreach ($dataResults as $dataResult) 
        {
            $columnNames = json_decode($dataResult['headers']);
            foreach ($columnNames as $columnName) 
            {
                if (!in_array(trim($columnName), $headers)) 
                {
                    $headers[] = trim($columnName);
                }
            }
        }

        return $headers;
    }

    /**
     * Return Data Headers
     *
     * @used
     * - DataController::dataAction
     * @param array $requestParams
     * @return array
     */
    public function getHeaders($requestParams)
    {
        $dataResults = $this->_getBuilder($requestParams, 'headers')
            ->getQuery()
            ->getArrayResult();

        return $this->_getColumnHeaders($dataResults);
    }
            
    /**
     * Return Report Headers
     *
     * @used
     * - ReportController::dataAction
     * @param array $requestParams
     * @return array
     */
    public function getReportHeaders($requestParams)
    {
        $reportResults = $this->_getReportBuilder($requestParams, 'headers')
            ->getQuery()
            ->getArrayResult();

        return $this->_getColumnHeaders($reportResults);
    }

    /**
     * Return Data Results
     * with DataTable Params:
     * - Pagination
     * - Ordering
     * - Filtering
     *
     * @used
     * - DataController::dataAction
     * @param array $requestParams
     * @param array $dataTableParams
     * @return array
     */
    public function getResults($requestParams, $dataTableParams)
    {
        // Post Request
        $this->_dataTableParams = $dataTableParams;

        // Data
        $dataBuilder = $this->_getBuilder($requestParams, 'data');
        $dataBuilder = $this->_addPaginationToBuilder($dataBuilder);
        $dataBuilder = $this->_addOrderingToBuilder($dataBuilder);
        $dataBuilder = $this->_addFilteringToBuilder($dataBuilder);
        $data = $dataBuilder->getQuery()->getArrayResult();

        // Free Memory
        unset($dataBuilder);

        // Records Total
        $recordsTotalBuilder = $this->_getBuilder($requestParams, 'count');
        $recordsTotal = $recordsTotalBuilder->getQuery()->getSingleScalarResult();

        // Free Memory
        unset($recordsTotalBuilder);

        // Records Filtered
        $recordsFilteredBuilder = $this->_getBuilder($requestParams, 'count');
        $recordsFilteredBuilder = $this->_addOrderingToBuilder($recordsFilteredBuilder);
        $recordsFilteredBuilder = $this->_addFilteringToBuilder($recordsFilteredBuilder);
        $recordsFiltered = $recordsFilteredBuilder->getQuery()->getSingleScalarResult();

        // Free Memory
        unset($recordsFilteredBuilder);

        return array(
            'data'            => $data,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        );
    }

    /**
     * Return Report Results
     * with DataTable Params:
     * - Pagination
     * - Ordering
     * - Filtering
     *
     * @used
     * - ReportController::dataAction
     * @param array $requestParams
     * @param array $dataTableParams
     * @return array
     */
    public function getReportResults($requestParams, $dataTableParams)
    {
        // Post Request
        $this->_dataTableParams = $dataTableParams;

        // Data
        $dataBuilder = $this->_getReportBuilder($requestParams, 'data');
        $dataBuilder = $this->_addPaginationToBuilder($dataBuilder);
        $dataBuilder = $this->_addOrderingToBuilder($dataBuilder);
        $dataBuilder = $this->_addFilteringToBuilder($dataBuilder, true);
        $data = $dataBuilder->getQuery()->getArrayResult();

        // Free Memory
        unset($dataBuilder);

        // Records Total
        $recordsTotalBuilder = $this->_getReportBuilder($requestParams, 'count');
        $recordsTotal = $recordsTotalBuilder->getQuery()->getSingleScalarResult();

        // Free Memory
        unset($recordsTotalBuilder);

        // Records Filtered
        $recordsFilteredBuilder = $this->_getReportBuilder($requestParams, 'count');
        $recordsFilteredBuilder = $this->_addOrderingToBuilder($recordsFilteredBuilder);
        $recordsFilteredBuilder = $this->_addFilteringToBuilder($recordsFilteredBuilder, true);
        $recordsFiltered = $recordsFilteredBuilder->getQuery()->getSingleScalarResult();

        // Free Memory
        unset($recordsFilteredBuilder);

        return array(
            'data'            => $data,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        );
    }

    /**
     * Return Export for Data
     *
     * @used
     * - DataController::exportData
     * @param array $requestParams
     * @param array $dataTableParams
     * @return array
     */
    public function getExportData($requestParams, $dataTableParams)
    {
        // Post Request
        $this->_dataTableParams = $dataTableParams;
        $this->_dataTableParams['order'] = json_decode($this->_dataTableParams['order'], true);
        $this->_dataTableParams['search'] = json_decode($this->_dataTableParams['search'], true);

        // Headers
        $headers = $this->getHeaders($requestParams);

        // Data
        $dataBuilder = $this->_getBuilder($requestParams, 'data');
        $dataBuilder = $this->_addOrderingToBuilder($dataBuilder);
        $dataBuilder = $this->_addFilteringToBuilder($dataBuilder, true);
        $data = $dataBuilder->getQuery()->getArrayResult();

        // Free Memory
        unset($dataBuilder);
        
        // Export Data
        $exportData = $this->getTableData($headers, $data, $requestParams['topicName']);

        // Prevent Memory Leaks
        unset($data);
        $this->getEntityManager()->clear();
        $this->getEntityManager()->getConnection()->getConfiguration()->setSQLLogger(null);

        return $exportData;
    }

    /**
     * Return Export for Report
     *
     * @used
     * - ReportController::exportData
     * @param array $requestParams
     * @param array $dataTableParams
     * @return array
     */
    public function getReportExportData($requestParams, $dataTableParams)
    {
        // Post Request
        $this->_dataTableParams = $dataTableParams;
        $this->_dataTableParams['order'] = json_decode($this->_dataTableParams['order'], true);
        $this->_dataTableParams['search'] = json_decode($this->_dataTableParams['search'], true);

        // Headers
        $headers = $this->getReportHeaders($requestParams);

        // Data
        $dataBuilder = $this->_getReportBuilder($requestParams, 'data');
        $dataBuilder = $this->_addOrderingToBuilder($dataBuilder);
        $dataBuilder = $this->_addFilteringToBuilder($dataBuilder, true);
        $data = $dataBuilder->getQuery()->getArrayResult();
        
        // Free Memory
        unset($dataBuilder);

        // Export Data
        $exportData = $this->getTableData($headers, $data);

        // Prevent Memory Leaks
        unset($data);
        $this->getEntityManager()->clear();
        $this->getEntityManager()->getConnection()->getConfiguration()->setSQLLogger(null);

        return $exportData;
    }

    /**
     * Return Periods In
     *
     * @used
     * - ReportController::indexAction
     * @param array $periods
     * @return array
     */
    public function getPeriodsIn($periods)
    {
        $periodsIn = array();
        foreach ($periods as $period)
        {
            if ($period['id'] != '0') 
            {
                if (!in_array($period['id'], $periodsIn)) 
                {
                    $periodsIn[$period['label']] = $period['id'];
                }
            }
        }

        return $periodsIn;
    }

    /**
     * Delete Data
     *
     * @used
     * - ManagerController::deleteAction
     * @param array $params
     * @return bool
     */
    public function deleteData($params)
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->update($this->getEntityName(), 'd')
            ->set('d.isActive', ':isActive')
            ->where('d.sourceId = :sourceId')
            ->andWhere('d.fileId = :fileId')
            ->andWhere('d.periodId = :periodId')
            ->setParameters(array(
                'isActive' => false,
                'sourceId' => $params['sourceId'],
                'fileId'   => $params['fileId'],
                'periodId' => $params['periodId']
            ))
            ->getQuery()
            ->execute();
    }
}
