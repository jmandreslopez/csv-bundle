<?php

namespace Sample\CsvBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sample\CsvBundle\Entity\SourceConfigurationTypes;

class SourcesController extends Controller
{
    // Menu Active
    public static $active = 'upload';

    /**
     * Sources
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return render
     */
    public function indexAction()
    {
        $sources = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:Sources')
            ->findAll();

        $i = 0;
        $rows = array();
        foreach ($sources as $source) 
        {
            $rows[$i]['sourceId']   = $source->getId();
            $rows[$i]['sourceName'] = $source->getName();
            $rows[$i]['isActive']   = $source->getIsActive();

            $configurations = $this->get('csv_functions.sources')
                ->getSourceConfigurations($source->getId());

            if (!empty($configurations)) {
                foreach ($configurations as $configuration) 
                {
                    switch($configuration->getSourceConfigurationTypeId()) 
                    {                        
                        case '1':   // Topic ID
                                    $rows[$i]['topicId'] = $configuration->getSetting();
                                    break;

                        
                        case '2':   // Prices
                                    $rows[$i]['price'] = $configuration->getSetting();
                                    break;

                        default:    break;
                    }
                }
            } else {
                $rows[$i]['topictId'] = '';
                $rows[$i]['price'] = '';
            }

            // Class
            $rows[$i]['class'] = ($source->getIsActive()) ? '' : $rows[$i]['class'] = 'text-muted';

            $i++;
        }

        // Create Form
        $form = $this->createFormBuilder(array())
            ->add('topictId', 'text', array(
                'label' => 'Topic ID')
            )
            ->add('price', 'text', array(
                'label' => 'Price')
            )
            ->add('sourceId', 'hidden')
            ->getForm()
            ->createView();

        return $this->render('SampleCsvBundle:Sources:index.html.twig', array(
            'rows'   => $rows,
            'form'   => $form,
            'active' => self::$active
        ));
    }

    /**
     * Sources :: Change Configuration Values
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|boolean
     */
    public function configurationAction(Request $request)
    {
        // Request Params
        $requestParams = $this->get('csv_functions.sources')
            ->getRequestParams($request);

        // Topic Id
        $configurationTopicId = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:SourceConfigurations')
            ->getSourceConfiguration($requestParams['sourceId'], SourceConfigurationTypes::$sourceConfigurationTopicId);

        if (!empty($configurationTopicId)) {
            $configurationTopicId->setSetting($requestParams['topictId']);
            $this->getDoctrine()->getEntityManager()->persist($configurationTopicId);
        } else {
            return false;
        }

        // Price
        $configurationPrice = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:SourceConfigurations')
            ->getSourceConfiguration($requestParams['sourceId'], SourceConfigurationTypes::$sourceConfigurationPrice);

        if (!empty($configurationPrice)) {
            $configurationPrice->setSetting($requestParams['price']);
            $this->getDoctrine()->getEntityManager()->persist($configurationPrice);
        } else {
            return false;
        }

        // Flush
        $this->getDoctrine()->getEntityManager()->flush();

        return new JsonResponse(array(
            'sourceId' => $requestParams['sourceId'],
            'topictId' => $configurationTopicId->getSetting(),
            'price'    => $configurationPrice->getSetting()
        ));
    }
}
