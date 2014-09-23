<?php

namespace Sample\CsvBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataController extends Controller
{
    // Menu Active
    public static $active = 'data';

    /**
     * Data
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return render
     */
    public function indexAction(Request $request)
    {
        // Request Params
        $requestParams = $this->get('csv_functions.data')
            ->getRequestParams($request);

        // Source Object
        $source = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:Sources')
            ->find($requestParams['sourceId']);

        // Period Object
        $period = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:Periods')
            ->find($requestParams['periodId']);

        if (!empty($source) && !empty($period))
        {
            // Data Headers
            $headers = $this->getDoctrine()
                ->getRepository('SampleCsvBundle:Data')
                ->getHeaders($requestParams);

            return $this->render('SampleCsvBundle:Data:index.html.twig', array(
                'headers' => $headers,
                'source'  => $source->getName(),
                'period'  => $period->getLabel(),
                'params'  => $requestParams,
                'active'  => self::$active
            ));
        }

        return $this->render('SampleCsvBundle::error.html.twig');
    }

    /**
     * Data :: Ajax Data
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function dataAction(Request $request)
    {
        // Request Params
        $requestParams = $this->get('csv_functions.data')
            ->getRequestParams($request, true);

        // DataTable Params
        $dataTableParams = $request->request->all();

        // Topic Name
        $requestParams['topicName'] = $this->get('csv_functions.data')
            ->getTopicName(array(
                'sourceId' => $requestParams['sourceId'],
                'topicId'  => $requestParams['topicId']
            ));

        // Data Headers
        $headers = $this->get('csv_functions.data')
            ->getHeaders($requestParams);

        // Data Rows
        $results = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:Data')
            ->getResults($requestParams, $dataTableParams);

        // Data
        $data = $this->get('csv_functions.data')
            ->getTableData($headers, $results['data'], $requestParams['topicName']);

        // Output
        $output = array(
            'draw'            => intval($dataTableParams['draw']),
            'data'            => $data,
            'recordsTotal'    => $results['recordsTotal'],
            'recordsFiltered' => $results['recordsFiltered']
        );

        unset($results);

        return new JsonResponse($output);
    }

    /**
     * Data :: Export Data
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Excel File
     */
    public function exportAction(Request $request)
    {
        // Increase Server Memory Limit
        ini_set('memory_limit', '1024M');

        // Excel File Params
        $fileParams = array(
            'name' => 'data',
            'ext'  => $this->container->getParameter('file.extension')
        );

        // Request Params
        $requestParams = $this->get('csv_functions.data')
            ->getRequestParams($request, true);

        // Add Topic Name to Request Params
        $requestParams['topicName'] = $this->get('csv_functions.data')
            ->getTopicName(array(
                'sourceId' => $requestParams['sourceId'],
                'topicId'  => $requestParams['topicId']
            ));

        // DataTable Params
        $dataTableParams = $request->request->all();

        // Data Headers
        $headers = $this->get('csv_functions.data')
            ->getHeaders($requestParams);

        // Data Array
        $data = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:Data')
            ->getExportData($requestParams, $dataTableParams);

        return $this->get('csv_functions.data')
            ->getExportResponse($headers, $data, $fileParams);
    }
}