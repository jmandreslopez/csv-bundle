<?php

namespace Sample\CsvBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class MultipleController extends Controller
{
    // Menu Active
    public static $active = 'upload';

    /**
     * Multiple
     *
     * @return render
     */
    public function indexAction()
    {
        $multipleConfigurations = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:MultipleConfigurations')
            ->findAll();

        if (empty($multipleConfigurations)) {

            // ERROR: No configurations
            $this->_log(1, '{Info: No configurations}');
        }

        $data = array();
        foreach ($multipleConfigurations as $multipleConfiguration)
        {
            switch ($multipleConfiguration->getName())
            {
                case 'multiple_id':     $data['multiple'] = $multipleConfiguration->getValue();
                                        break;

                case 'topic_id':        $data['topic'] = $multipleConfiguration->getValue();
                                        break;

                case 'percentage':      $data['percentage'] = $multipleConfiguration->getValue();
                                        break;

                default:                break;
            }
        }

        // Create Form
        $form = $this->createFormBuilder(array())
            ->add('multipleId', 'text', array(
                'label' => 'Multiple ID:',
                'data'  => $data['multiple'])
            )
            ->add('topicId', 'text', array(
                'label' => 'Topic ID:',
                'data'  => $data['topic'])
            )
            ->add('percentage', 'text', array(
                'label' => 'Percentage:',
                'data'  => $data['percentage'])
            )
            ->getForm()
            ->createView();

        return $this->render('SampleCsvBundle:Multiple:index.html.twig', array(
            'form'   => $form,
            'active' => self::$active
        ));
    }

    /**
     * Multiple :: Change Configuration Values
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function configurationAction(Request $request)
    {
        // Request Params
        $requestParams = $this->get('csv_functions.multiple')
            ->getRequestParams($request);

        $multipleConfigurations = $this->getDoctrine()
            ->getRepository('SampleCsvBundle:MultipleConfigurations')
            ->findAll();

        if (empty($multipleConfigurations)) {

            // ERROR: No configurations
            $this->get('csv_functions.multiple')
                ->log(1, '{Info: No configurations}');
        }

        foreach ($multipleConfigurations as $multipleConfiguration)
        {
            switch ($multipleConfiguration->getName())
            {
                case 'multiple_id':     $multipleConfiguration->setValue($requestParams['multipleId']);
                                        break;

                case 'topic_id':        $multipleConfiguration->setValue($requestParams['topicId']);
                                        break;

                case 'percentage':      $multipleConfiguration->setValue($requestParams['percentage']);
                                        break;

                default:                break;
            }

            // Persist
            $this->getDoctrine()->getEntityManager()->persist($multipleConfiguration);
        }

        // Save
        $this->getDoctrine()->getEntityManager()->flush();

        return new JsonResponse($requestParams);
    }
}
