<?php

namespace Sample\CsvBundle\Charts;

class StackedChart
{
    protected $_entityManager;

    public function __construct($entityManager)
    {
        $this->_entityManager = $entityManager;
    }

    /**
     * Return Line Chart
     *
     * @used
     * - GraphController::indexAction
     * @param array $periods
     * @param array $requestParams
     * @return javascript
     */
    public function getChart($periods, $requestParams)
    {
        // Series
        $series = array();

        $labels = array();

        $totals = array();

        $options = '';

        // All Sources
        $sources = $this->_entityManager
            ->getRepository('SampleCsvBundle:Sources')
            ->findBy(array(
                'isActive' => true
            ));

        $flag = false;
        foreach ($sources as $source)
        {
            // Values
            $values = array();

            $i = 0;
            foreach ($periods as $period)
            {
                if (!$flag) {
                    $labels[] = '\''.$period['label'].'\'';
                }

                // SourceId
                $requestParams['sourceId'] = $source->getId();

                // Point
                $periodPrice = $this->_entityManager
                    ->getRepository('SampleCsvBundle:Prices')
                    ->getFiscalCost($period['id'], $requestParams, false, 0);

                $values[$i] = $periodPrice;

                if (empty($totals[$i])) { 
                    $totals[$i] = 0;
                }
                $totals[$i] += $periodPrice;

                $i++;
            }
            $flag = true;

            // Values Format
            $series[] = $this->_setFormat($values);

            $options .= '{ highlighter: { formatString: "'.$source->getName().': %s" }}, ';
        }

        $series[] = $this->_setFormat($totals);

        $options .= '{
            disableStack : true,
            renderer: $.jqplot.LineRenderer,
            showLine:false,
            pointLabels: {
                show: true
            },
            markerOptions: {
                show: false,
                size: 0
            },
            highlighter: { formatString: "Total: %s" }
        }';

        // Series Format
        $chartFormat = $this->_setFormat($series);

        return array(
            'labels'  => $this->_setFormat($labels),
            'data'    => $chartFormat,
            'options' => '['.$options.']'
        );
    }

    /**
     * [PRIVATE]
     * Convert Data into jqPlot Format
     *
     * @used
     * - self::getChart
     * @param array $values
     * @return javascript
     */
    private function _setFormat($values)
    {
        // Initialize
        $data = '';

        $i = 0;
        $len = count($values);
        foreach ($values as $value) {
            $data .= $value;
            $data .= ($i != $len - 1) ? ', ' : '';
            $i++;
        }

        return '['.$data.']';
    }

    /**
     * Return Line Chart Angle
     * for the ticks
     *
     * @used
     * - GraphController::indexAction
     * @param integer $count
     * @return string
     */
    public function getAngle($count)
    {
        return ($count < 9) ? '0' : '-90';
    }
}