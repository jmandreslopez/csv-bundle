<?php

namespace Sample\CsvBundle\Entity;

use Sample\CsvBundle\Entity\SharedRepository;

/**
 * MultipleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MultipleRepository extends SharedRepository
{
    /**
     * Return Multiple for Period Id
     *
     * @used
     * - ImportCommand::_importMultiple
     * - ImportCommand::_importCsv
     * @param integer $periodId
     * @return array
     */
    public function getMultiple($periodId)
    {
        $multiple = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT(m.multipleId) AS multiple')
            ->from($this->getEntityName(), 'm')
            ->where('m.periodId = :periodId')
            ->setParameter('periodId', $periodId)
            ->getQuery()
            ->getArrayResult();

        $array = array();
        foreach ($multiple as $multi) {
            $array[] = $multi['multiple'];
        }

        return $array;
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
            ->update($this->getEntityName(), 'm')
            ->set('m.isActive', ':isActive')
            ->where('m.sourceId = :sourceId')
            ->andWhere('m.fileId = :fileId')
            ->andWhere('m.periodId = :periodId')
            ->setParameters(array(
                'isActive'  => false,
                'sourceId'  => $params['sourceId'],
                'fileId'    => $params['fileId'],
                'periodId'  => $params['periodId']
            ))
            ->getQuery()
            ->execute();
    }
}
