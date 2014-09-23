<?php

namespace Sample\CsvBundle\Functions;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Sample\CsvBundle\Entity\General;

class Shared
{
    protected $_entityManager;

    public function __construct($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Return Sources Array
     *
     * @used
     * - UploadController::indexAction
     * - ReportController::indexAction
     * @param boolean $inArray DEFAULT false
     * @return array
     */
    public function getSources($inArray = false)
    {
        $sources = $this->_entityManager
            ->getRepository('SampleCsvBundle:Sources')
            ->findBy(array(
                'isActive' => true
            ));

        if ($inArray) {
            $sourcesArray = array('' => '');
            foreach ($sources as $source) {
                $sourcesArray[$source->getId()] = $source->getName();
            }
        } else {
            $sourcesArray = $source;
        }

        return $sourcesArray;
    }

    /**
     * Return all the available topics
     * with the ID and the Name
     *
     * @used
     * - GraphController::indexAction
     * - ReportController::indexAction
     * @return array
     */
    public function getTopicsData()
    {
        return $this->_entityManager
            ->getRepository('SampleCsvBundle:Data')
            ->getTopicsData();
    }

    /**
     * Return the Topic Name for a
     * Source ID and a Topic ID
     *
     * @used
     * - DataController::dataAction
     * @param array $params
     * @return string
     */
    public function getTopicName($params)
    {
        return $this->_entityManager
            ->getRepository('SampleCsvBundle:General')
            ->getTopicName(array(
                'sourceId' => $params['sourceId'],
                'topicId'  => $params['topicId']
            ));
    }

    /**
     * Return the Topic Names in the
     * Topics Data Array, sort them
     * and remove the 'Not Available'
     *
     * @used
     * - GraphController::indexAction
     * - ReportController::indexAction
     * @param array $topicsData
     * @return sorted array
     */
    public function getTopicNames($topicsData)
    {
        // Topic Names
        $topicNames = $topicsData['topicNames'];

        // Sort
        asort($topicNames);

        // Delete Not Available
        $na = array_search(General::$nameNotAvailable, $topicNames);
        unset($topicNames[$na]);

        return $topicNames;
    }

    /**
     * [PRIVATE]
     * Return current date with
     * established delay
     *
     * @used
     * - self::getPeriodInterval
     * @return date
     */
    private function getCurrentDate()
    {
        return strtotime(date('Y-m-d', strtotime(date('Y-m-d'))).'-1 month');
    }

    /**
     * Return the period interval from
     * a request
     *
     * @used
     * - GraphController::indexAction
     * - ReportController::indexAction
     * @param Request $request
     * @return array
     */
    public function getPeriodInterval($request)
    {
        // Period Interval Array
        $periodInterval = array();

        // End: Month
        $endMonth = $request->query->get('m2');
        $periodInterval['end']['m'] = (isset($endMonth)) ? $endMonth : date('n', $this->getCurrentDate());

        // End: Year
        $endYear = $request->query->get('y2');
        $periodInterval['end']['y'] = (isset($endYear)) ? $endYear : date('Y', $this->getCurrentDate());

        // Start: Month
        $startMonth = $request->query->get('m1');
        if (isset($startMonth)) {
            $periodInterval['start']['m'] = $startMonth;
        } else {
            if ($periodInterval['end']['m'] <= 4) {
                $month = $periodInterval['end']['m'] - 4;
                switch ($month) {
                    case 0:     $periodInterval['start']['m'] = 12;
                                break;
                    case -1:    $periodInterval['start']['m'] = 11;
                                break;
                    case -2:    $periodInterval['start']['m'] = 10;
                                break;
                    case -3:    $periodInterval['start']['m'] = 9;
                                break;
                    default:    break;
                }
            } else {
                $periodInterval['start']['m'] = $periodInterval['end']['m'] - 4;
            }
        }

        // Start: Year
        $startYear = $request->query->get('y1');
        if (isset($startYear)) {
            $periodInterval['start']['y'] = $startYear;
        } else {
            if ($periodInterval['end']['m'] <= 4) {
                $periodInterval['start']['y'] = $periodInterval['end']['y'] - 1;
            } else {
                $periodInterval['start']['y'] = $periodInterval['end']['y'];
            }
        }

        return $periodInterval;
    }

    /**
     * Return Periods for the
     * Period Interval
     *
     * @used
     * - ReportController::indexAction
     * @param array $periodInterval
     * @return array
     */
    public function getPeriods($periodInterval)
    {
        return $this->_entityManager
            ->getRepository('SampleCsvBundle:Periods')
            ->getPeriods($periodInterval);
    }

    /**
     * Return Select Options for the
     * Topic Params
     *
     * @used
     * - GraphController::indexAction
     * - ReportController::indexAction
     * @param array $topicParams
     * @return html
     */
    public function getSelectOptions($topicParams)
    {
        $selectOptions = array();
        foreach ($topicParams as $topicKey => $param)
        {
            $options = '';
            foreach ($param['values'] as $key => $value)
            {
                $key = ($key == '0') ? $value : $key;
                $optionValue = ($param['isKey']) ? $key : $value;

                $options .= '<option value="'.$optionValue.'"';
                if (!empty($param['selected'])) {
                    if (is_array($param['selected'])) {
                        if (in_array($optionValue, $param['selected'])) {
                            $options .= ' selected';
                        }
                    } else {
                        if ($optionValue == $param['selected']) {
                            $options .= ' selected';
                        }
                    }
                }
                $options .= '>'.$value.'</option>';
            }

            $selectOptions[$topicKey] = $options;
        }

        return $selectOptions;
    }

    /**
     * Return Dates Defaults for the
     * jQuery Date Picker Plugin
     *
     * @used
     * - GraphController::indexAction
     * - ReportController::indexAction
     * @param array $periodInterval
     * @return javascript
     */
    public function getDatesDefaults($periodInterval)
    {
        $startMonth = $periodInterval['start']['m'];
        $startYear  = $periodInterval['start']['y'];
        $endMonth   = $periodInterval['end']['m'];
        $endYear    = $periodInterval['end']['y'];

        $start = $startMonth.'/1/'.$startYear;
        $end   = $endMonth.'/1/'.$endYear;

        return array(
            'startDefaultDate' => $start,
            'startMaxDate'     => $end,
            'endDefaultDate'   => $end,
            'endMinDate'       => $start,
            'endMaxDate'       => date('n/j/Y')
        );
    }

    /**
     * Return Request Params into View
     * format, the View Params
     *
     * @used
     * - GraphController::indexAction
     * - ReportController::indexAction
     * @param array $requestParams
     * @return array
     */
    public function getViewParams($requestParams)
    {
        $viewParams = array();
        foreach ($requestParams as $key => $value) {
            $viewParams[$key] = json_encode($value);
        }

        return $viewParams;
    }

    /**
     * Return Table Headers
     *
     * @used
     * - DataController::exportAction
     * - ReportController::exportAction
     * @param excelObject $phpExcelObject
     * @param array $headers
     * @return excelObject
     */
    public function getTableHeaders($phpExcelObject, $headers)
    {
        // Pre
        $pre = '';
        $preCont = 65;

        $col = 65; // "A" Ascii Code
        foreach ($headers as $header)
        {
            // Cell
            $cell = $pre.chr($col).'1';

            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue($cell, $header);

            if ($col < 90) {
                $col++;
            } else {
                $col = 65;
                $pre = chr($preCont);
                $preCont++;
            }
        }

        return $phpExcelObject;
    }

    /**
     * Return Table Data
     *
     * @used
     * - DataController::dataAction
     * - ReportController::dataAction
     * @param array $headers
     * @param array $data
     * @param string $topicName DEFAULT null
     * @return array
     */
    public function getTableData($headers, $data, $topicName = null)
    {
        return $this->_entityManager
            ->getRepository('SampleCsvBundle:Data')
            ->getTableData($headers, $data, $topicName);
    }

    /**
     * Return Export Response with
     * the CSV file attached
     *
     * @used
     * - GraphController::exportAction
     * - DataController::exportAction
     * - ReportController::exportAction
     * @param type $headers
     * @param type $data
     * @param type $fileParams
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getExportResponse($headers, $data, $fileParams)
    {
        $response = new StreamedResponse(function() use($headers, $data) {
            
            // File Handle
            $handle = fopen('php://output', 'r+');

            // Add Data
            fputcsv($handle, $headers);
            foreach ($data as $d) {
                fputcsv($handle, $d);
            }

            // Close Handle
            fclose($handle);
        });

        // Attachment File
        $attachmentFile = $fileParams['name'].'.'.$fileParams['ext'];

        // Adding Excel Headers
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment;filename='.$attachmentFile);
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }

    /**
     * [PROTECTED]
     * Log
     *
     * @used
     * - MultipleController::configurationAction
     * @param integer $logType
     * @param string $description
     */
    protected function log($logType, $description)
    {
        $this-$this->_entityManager
            ->getRepository('SampleCsvBundle:Log')
            ->log($logType, 'MultipleController', $description);
    }
}

