<?php

namespace Sample\CsvBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sample\CsvBundle\Entity\Files;

class ManagerController extends Controller
{
    // Menu Active
    public static $active = 'manager';

    /**
     * Manager
     *
     * @return render
     */
    public function indexAction()
    {
        // Knp Paginator
        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $this->get('csv_functions.manager')->getPaginationQuery(),
            $this->get('request')->query->get('page', 1),
            10 /* limit per page */
        );

        return $this->render('SampleCsvBundle:Manager:index.html.twig', array(
            'pagination' => $pagination,
            'active'     => self::$active
        ));
    }

    /**
     * Manager :: Disable
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function disableAction(Request $request)
    {
        // Request File Id
        $fileId = $request->query->get('id');

        if (!empty($fileId)) 
        {            
            // File Object
            $file = $this->getDoctrine()
                ->getRepository('SampleCsvBundle:Files')
                ->find($fileId);

            if (!empty($file)) 
            {
                $file->setStatus('disabled');

                // Save to DB
                $this->getDoctrine()->getEntityManager()->persist($file);
                $this->getDoctrine()->getEntityManager()->flush();

                return new JsonResponse(array(
                    'fileId' => $file->getId()
                ));
            }
        }

        return false;
    }

    /**
     * Manager :: Delete
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteAction(Request $request)
    {
        // Request File Id
        $fileId = $request->query->get('id');

        if (!empty($fileId)) 
        {
            // File Object
            $file = $this->getDoctrine()
                ->getRepository('SampleCsvBundle:Files')
                ->find($fileId);

            if (!empty($file)) 
            {
                $params = array(
                    'sourceId' => $file->getSourceId(),
                    'fileId'   => $file->getId(),
                    'periodId' => $file->periodId()
                );

                // Type: CSV
                if ($file->getFileTypeId() === Files::$fileTypeCsv) {
                    
                    // Price
                    $this->getDoctrine()
                        ->getRepository('SampleCsvBundle:Prices')
                        ->deleteData($params);

                    // Data
                    $this->getDoctrine()
                        ->getRepository('SampleCsvBundle:Data')
                        ->deleteData($params);
                    
                } else {
                    
                    // Multiple
                    $this->getDoctrine()
                        ->getRepository('SampleCsvBundle:Multiple')
                        ->deleteData($params);
                    
                }

                // Change file status to Deleted
                $file->setStatus('deleted');

                // Save to DB
                $this->getDoctrine()->getEntityManager()->persist($file);
                $this->getDoctrine()->getEntityManager()->flush();

                return new JsonResponse(array(
                    'fileId' => $file->getId()
                ));
            }
        }

        return false;
    }
}