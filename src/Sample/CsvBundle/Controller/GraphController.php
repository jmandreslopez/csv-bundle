<?php

namespace Sample\CsvBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class GraphController extends Controller
{
    // Menu Active
    public static $active = 'graph';

    /**
     * Graph
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return render
     */
    public function indexAction(Request $request)
    {
        // Topic Data
        $topicsData = $this->get('csv_functions.graph')
            ->getTopicsData();

        if (!empty($topicsData['topicIds'])
            && !empty($topicsData['topicNames'])
                && !empty($topicsData['franchises'])) {
            
            // Request Params
            $requestParams = $this->get('csv_functions.graph')
                ->getRequestParams($request);

            // Period Interval
            $requestParams['periodInterval'] = $this->get('csv_functions.graph')
                ->getPeriodInterval($request);

            // Periods for the Period Interval
            $periods = $this->get('csv_functions.graph')
                ->getPeriods($requestParams['periodInterval']);

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
            $topicNames = $this->get('csv_functions.graph')
                ->getTopicNames($topicsData);
            
            $topicsParams['topicNames']= array(
                'values'   => $topicNames,
                'selected' => $requestParams['topicName'],
                'isKey'    => false
            );

            // Select Options
            $selectOptions = $this->get('csv_functions.graph')
                ->getSelectOptions($topicsParams);

            // Stacked Chart
            $stackedChartData = $this->get('csv_charts.stacked')
                ->getChart($periods, $requestParams);

            // Stacked Chart Angle
            $stackedChartAngle = $this->get('csv_charts.stacked')
                ->getAngle(count($periods));

            // Dates Defaults
            $datesDefaults = $this->get('csv_functions.graph')
                ->getDatesDefaults($requestParams['periodInterval']);

            // Path
            $path = $this->get('csv_functions.graph')
                ->getPath($requestParams);

            return $this->render('SampleCsvBundle:Graph:index.html.twig', array(
                'periods'           => $periods,
                'selectOptions'     => $selectOptions,
                'stackedChartData'  => $stackedChartData,
                'stackedChartAngle' => $stackedChartAngle,
                'datesDefaults'     => $datesDefaults,
                'path'              => $path,
                'active'            => self::$active
            ));
        }

        return $this->render('SampleCsvBundle::empty.html.twig');
    }

    /**
     * Graph :: Ajax Data
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function dataAction(Request $request)
    {
        // Request Params
        $requestParams = $this->get('csv_functions.graph')
            ->getRequestParams($request);

        // Period Interval
        $requestParams['periodInterval'] = $this->get('csv_functions.graph')
            ->getPeriodInterval($request);

        // Path
        $path = $this->generateUrl('data');

        // Generals
        $generals = $this->get('csv_functions.graph')
            ->getGenerals($requestParams, $path);

        return new JsonResponse($generals);
    }

    /**
     * Graph :: Excel Data
     *
     * @return type
     */
    public function exportAction(Request $request)
    {
        // Increase Server Memory Limit
        ini_set('memory_limit', '1024M');

        // Excel File Params
        $fileParams = array(
            'name' => 'graph',
            'ext'  => $this->container->getParameter('file.extension')
        );

        // Data to Export
        $tableData = json_decode($request->request->get('data'), true);

        // Graph Headers
        $headers = $tableData[0];
        
        // Free Memory
        unset($tableData[0]);

        // Graph Data
        $data = $tableData;

        return $this->get('csv_functions.graph')
            ->getExportResponse($headers, $data, $fileParams);
    }
}
