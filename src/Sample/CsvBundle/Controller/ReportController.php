<?php

namespace Sample\CsvBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportController extends Controller
{
    // Menu Active
    public static $active = 'report';

    /**
     * Report
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return render
     */
    public function indexAction(Request $request)
    {
        // Topic Data
        $topicsData = $this->get('csv_functions.report')
            ->getTopicsData();

        if (!empty($topicsData)) {

            // Request Params
            $requestParams = $this->get('csv_functions.report')
                ->getRequestParams($request);

            // Period Interval
            $periodInterval = $this->get('csv_functions.report')
                ->getPeriodInterval($request);

            // Periods for the Period Interval
            $periods = $this->get('csv_functions.report')
                ->getPeriods($periodInterval);

            // Add Periods to Request Params
            $requestParams['periods'] = $this->getDoctrine()
                ->getRepository('SampleCsvBundle:Data')
                ->getPeriodsIn($periods);

            // Source Ids
            $topicsParams['sourceIds']= array(
                'values'   => $this->get('csv_functions.report')->getSources(true),
                'selected' => $requestParams['sourceId'],
                'isKey'    => true
            );

            // Franchises
            $topicsParams['franchises']= array(
                'values'   => $topicsData['franchises'],
                'selected' => $requestParams['franchise'],
                'isKey'    => false
            );

            // Topic Ids
            ksort($topicsData['topicIds']);
            $topicsParams['topicIds']= array(
                'values'   => $topicsData['topicIds'],
                'selected' => $requestParams['topicId'],
                'isKey'    => false
            );

            // Topic Names
            $topicNames = $this->get('csv_functions.report')
                ->getTopicNames($topicsData);
            $topicsParams['topicNames']= array(
                'values'   => $topicNames,
                'selected' => $requestParams['topicName'],
                'isKey'    => false
            );

            // Select Options
            $selectOptions = $this->get('csv_functions.report')
                ->getSelectOptions($topicsParams);

            // Dates Defaults
            $datesDefaults = $this->get('csv_functions.report')
                ->getDatesDefaults($periodInterval);

            // View Params
            $viewParams = $this->get('csv_functions.report')
                ->getViewParams($requestParams);

            // Report Headers
            $headers = $this->getDoctrine()
                ->getRepository('SampleCsvBundle:Data')
                ->getReportHeaders($requestParams);

            return $this->render('SampleCsvBundle:Report:index.html.twig', array(
                'selectOptions' => $selectOptions,
                'datesDefaults' => $datesDefaults,
                'headers'       => $headers,
                'params'        => $viewParams,
                'active'        => self::$active
            ));
        }

        return $this->render('SampleCsvBundle::error.html.twig');
    }

    /**
     * Report :: Ajax Data
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function dataAction(Request $request)
    {
        // Request Params
        $requestParams = $this->get('csv_functions.report')
            ->getRequestParams($request, true);

        // Add Periods to Request Params
        $requestParams['periods'] = json_decode($request->request->get('periods'), true);

        // DataTable Params
        $dataTableParams = $request->request->all();

        // Report Headers
        $headers = $this->get('csv_functions.report')
            ->getHeaders($requestParams);

        // Report Rows
        $results = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:Data')
            ->getReportResults($requestParams, $dataTableParams);

        // Data
        $data = $this->get('csv_functions.report')
            ->getTableData($headers, $results['data']);

        // Output
        $output = array(
            'draw'            => intval($dataTableParams['draw']),
            'data'            => $data,
            'recordsTotal'    => $results['recordsTotal'],
            'recordsFiltered' => $results['recordsFiltered']
        );

        // Free Memory
        unset($results);

        return new JsonResponse($output);
    }

    /**
     * Report :: Export Data
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
            'name' => 'report',
            'ext'  => $this->container->getParameter('file.extension')
        );

        // Request Params
        $requestParams = $this->get('csv_functions.report')
            ->getRequestParams($request, true);

        // Add Periods to Request Params
        $requestParams['periods'] = json_decode($request->request->get('periods'), true);

        // DataTable
        $dataTableParams = $request->request->all();

        // Headers
        $headers = $this->get('csv_functions.report')
            ->getHeaders($requestParams);

        // Data
        $data = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:Data')
            ->getReportExportData($requestParams, $dataTableParams);

        return $this->get('csv_functions.report')
            ->getExportResponse($headers, $data, $fileParams);
    }
}
