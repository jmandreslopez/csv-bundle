<?php

namespace Sample\CsvBundle\Form;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormBuilderInterface;

use Avro\CsvBundle\Form\Type\ImportFormType as BaseImportFormType;

class ImportFormType extends BaseImportFormType
{
    private $sources;

    public function __construct($sources)
    {
        $this->sources = $this->processSources($sources);
    }

    /**
     * Build form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', 'choice', array(
                'label'   => 'Type:',
                'choices' => array(
                    '1' => 'CSV',
                    '2' => 'Multiple'
                )
            ))
            ->add('delimiter', 'choice', array(
                'label'   => 'Delimiter:',
                'choices' => array(
                    ',' => '[ , ] Comma ',
                    ';' => '[ ; ] Semicolon ',
                    '|' => '[ | ] Pipe ',
                    ':' => '[ : ] Colon'
                )
            ))
            ->add('source', 'choice', array(
                'label'    => 'Source:',
                'required' => true,
                'choices'  => $this->sources
            ))
            ->add('month', 'choice', array(
                'required' => true,
                'choices'  => $this->_getMonths(),
                'data'     => date('M')
            ))
            ->add('year', 'choice', array(
                'required' => true,
                'choices'  => $this->_getYears()
            ))
            ->add('fields', 'collection', array(
                'label'     => 'Fields',
                'required'  => false,
                'type'      => 'choice',
                'options'   => array(
                    'choices' => $options['field_choices']
                ),
                'allow_add' => true
            ));
    }

    /**
     * Process Sources Types
     *
     * @used
     * - self::__construct
     * @param array $sources
     * @return array
     */
    private function processSources($sources)
    {
        $processed = array();
        foreach ($sources as $source) {
            $processed[$source->getId()] = $source->getName();
        }

        return $processed;
    }

    /**
     * [PRIVATE]
     * Get Months array
     * 
     * @used
     * - self::buildForm
     * @return array
     */
    private function _getMonths()
    {
        return array(
            "Jan" => "January",
            "Feb" => "February",
            "Mar" => "March",
            "Apr" => "April",
            "May" => "May",
            "Jun" => "June",
            "Jul" => "July",
            "Aug" => "August",
            "Sep" => "September",
            "Oct" => "October",
            "Nov" => "November",
            "Dec" => "December");
    }

    /**
     * [PRIVATE]
     * Get Years array
     * 
     * @used
     * - self::buildForm
     * @return array
     */
    private function _getYears()
    {
        $years = array( date('y') => date('Y') );
        for ($i = 1, $j = 2001; $j < date('Y'); $i++, $j++) {
            $time = ($i == 1) ? "-1 year" : "-".$i." years";
            $years[date('y', strtotime($time, time()))]
                = date('Y', strtotime($time, time()));
        }

        return $years;
    }
}
