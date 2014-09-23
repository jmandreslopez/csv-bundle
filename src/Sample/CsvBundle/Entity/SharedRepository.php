<?php

namespace Sample\CsvBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Sample\CsvBundle\Entity\General;
use Sample\CsvBundle\Entity\Data;

/**
 * GeneralRepository
 */
class SharedRepository extends EntityRepository
{
    /**
     * [PROTECTED]
     * If the param is '[EMPTY]'
     * returns an empty array
     *
     * @used
     * - self::_getRequestResults
     * @param array $param
     * @return array
     */
    protected function _isEmpty($param)
    {
        if ($param[0] == General::$emptyField || empty($param) || !isset($param)) {
            $param = array();
        }

        return $param;
    }

    /**
     * [PROTECTED]
     * Return the Topic Ids for the
     * Requested Params
     *
     * @used
     * - PricesRepository::getPeriod
     * - PricesRepository::getSourcesInPeriod
     * @param type $requestParams
     * @return type
     */
    protected function _getTopicIds($requestParams)
    {
        // Franchise
        $franchise = $this->_isEmpty($requestParams['franchise']);

        // Topic Id
        $topicId = $this->_isEmpty($requestParams['topicId']);

        // Topic Name
        $topicName = $this->_isEmpty($requestParams['topicName']);

        if (empty($topicId) && empty($topicName)) {

            // Is Franchise Empty?
            $return = (!empty($franchise)) ? $this->_getFranchisesInResults($franchise) : array();

        } else {

            // Convert TopicNames into TopicIds
            $topicName = $this->_getTopicNameIds($topicName);

            // Merge Arrays
            $topicsIn = array_unique(array_merge($topicId, $topicName));

            if (!empty($franchise)) {
                $topicsIn = $this->_getFranchisesInResults($franchise, $topicsIn);
            }

            $return = $topicsIn;
        }

        return $return;
    }

    /**
     * [PROTECTED]
     * Change Topic Names into Topic Ids 
     * from the General Table
     *
     * @used
     * - GeneralRepository::_getRequestResults
     * - DataRepository::_getTopicIds
     * @param type $topicName
     * @return type
     */
    protected function _getTopicNameIds($topicName)
    {
        $results = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT(g.topicId) AS topicId')
            ->from('SampleCsvBundle:General', 'g')
            ->where('g.topicName IN (:topicName)')
            ->setParameter('topicName', $topicName)
            ->getQuery()
            ->getArrayResult();

        $topicIds = array();
        foreach ($results as $result) 
        {
            if (!in_array($result['topicId'], $topicIds)) 
            {
                $topicIds[] = $result['topicId'];
            }
        }

        return $topicIds;
    }

    /**
     * Return Topic Data array
     *
     * @used
     * - Shared::getTopicName
     * @return array
     */
    public function getTopicsData()
    {
        $topicIds = array();
        $topicNames = array();
        $franchises = array();

        // Topics
        $topicResults = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('g.topicId, g.topicName')
            ->from('SampleCsvBundle:General', 'g')
            ->where('g.isActive = true')
            ->groupBy('g.topicId')
            ->orderBy('g.topicId')
            ->getQuery()
            ->getArrayResult();

        foreach ($topicResults as $key => $value)
        {
            // Topic Id
            if (!in_array($value['topicId'], $topicIds)) {
                $topicIds[$key] = $value['topicId'];
            }

            // Topic Name
            if (!in_array($value['topicName'], $topicNames)) {
                $topicNames[] = $value['topicName'];
            }
        }
        
        // Free Memory
        unset($topicResults);

        // Franchises
        $franchiseResults = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT(p.franchise) AS franchise')
            ->from('SampleCsvBundle:Partial', 'p')
            ->where('p.topicTitle IN (:topicTitles)')
            ->orderBy('p.franchise', 'ASC')
            ->setParameter('topicTitles', $topicNames)
            ->getQuery()
            ->getArrayResult();

        foreach ($franchiseResults as $franchise)
        {
            if (!in_array($franchise['franchise'], $franchises)) {
                $franchises[$franchise['franchise']] = $franchise['franchise'];
            }
        }
        
        // Free Memory
        unset($franchiseResults);

        return array(
            'topicIds'   => $topicIds,
            'topicNames' => $topicNames,
            'franchises' => $franchises
        );
    }

    /**
     * [PRIVATE]
     * Get Topic Ids for the
     * submitted Franchise
     *
     * @used
     * - self::_getFranchisesInResults
     * @param string $franchise
     * @return array
     */
    private function _getFranchiseTopicIds($franchise)
    {
        $results = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT(p.topicId) AS topicId')
            ->from('SampleCsvBundle:Partial', 'p')
            ->innerJoin(
                'SampleCsvBundle:General', 'g',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'p.topicTitle = g.topicName AND g.isActive = true'
            )
            ->where('p.franchise = :franchise')
            ->setParameters(array(
                'franchise' => $franchise
            ))
            ->orderBy('g.topicId')
            ->getQuery()
            ->getArrayResult();

        $topicIds = array();
        foreach ($results as $result) {
            $topicIds[] = $result['topicId'];
        }

        return $topicIds;
    }

    /**
     * [PROTECTED]
     * Get Franchises In General Results
     *
     * @used
     * - GeneralRepository::_getRequestResults
     * - DataRepository::_getTopicIds
     * @param array $franchises
     * @param array $topicsIn DEFAULT array()
     * @return array
     */
    protected function _getFranchisesInResults($franchises, $topicsIn = array())
    {
        $topicIds = array();
        foreach ($franchises as $franchise)
        {
            foreach ($this->_getFranchiseTopicIds($franchise) as $topicId)
            {
                if (!in_array($topicId, $topicIds)) 
                {
                    $topicIds[] = $topicId;
                }
            }
        }

        if (!empty($topicsIn)) {
            $topicIds = array_unique(array_merge($topicsIn, $topicIds));
        }

        return $topicIds;
    }

    /**
     * Return Table Data
     *
     * @used
     * - Shared::getTableData
     * - DataRepository::getExportData
     * - DataRepository::getReportExportData
     * @param array $headers
     * @param array $results
     * @param string $topicName DEFAULT null
     * @param int $i DEFAULT 0
     * @return array
     */
    public function getTableData($headers, $results, $topicName = null, $i = 0)
    {
        $data = array();
        foreach ($results as $result)
        {
            // Copy of the main headers
            $partialHeaders = $headers;

            // Topic Name
            $data[$i][$partialHeaders[0]] = ($topicName === null) ? $result['topicName'] : $topicName;
            unset($partialHeaders[0]);

            // Topic Id
            $data[$i][$partialHeaders[1]] = $result['topicId'];
            unset($partialHeaders[1]);

            // Price
            $data[$i][$partialHeaders[2]] = $this->getFormatedPrice($result['price']);
            unset($partialHeaders[2]);

            // Multiple
            $data[$i][$partialHeaders[3]] = (!empty($result['multipleId'])) ? $result['multipleId'] : '';
            unset($partialHeaders[3]);

            $extraHeaders = json_decode($result['headers']);
            $extraValues = json_decode($result['values']);

            for ($j = 0; $j < count($partialHeaders); $j++) 
            {
                if (in_array($partialHeaders[$j+4], $extraHeaders)) {
                    $key = array_search($partialHeaders[$j+4], $extraHeaders);
                    $data[$i][$partialHeaders[$j+4]] = $extraValues[$key];
                } else {
                    $data[$i][$partialHeaders[$j+4]] = '';
                }
            }

            // Free Memory
            unset($extraHeaders);
            unset($extraValues);
            unset($partialHeaders);
            
            $i++;
        }

        return $data;
    }

    /**
     * Return Formated Price
     *
     * @used
     * - self::getTableData
     * @param integer $price
     * @return string
     */
    public function getFormatedPrice($price)
    {
        return '$'.number_format($price, Data::$decimalNumbers, Data::$decimalSymbol, Data::$thousandSymbol);
    }
}