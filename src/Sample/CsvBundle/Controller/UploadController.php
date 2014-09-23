<?php

namespace Sample\CsvBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sample\CsvBundle\Form\ImportFormType;

class UploadController extends Controller
{
    // Menu Active
    public static $active = 'upload';

    /**
     * Upload
     *
     * @return render
     */
    public function indexAction()
    {
        // Get Sources
        $sources = $this->get('csv_functions.upload')
            ->getSources();

        // Get default parameter
        $class = $this->container
            ->getParameter(sprintf('avro_csv.objects.%s.class', 'csv'));

        // Get field choices
        $field_choices = $this->container
            ->get('avro_csv.field_retriever')
            ->getFields($class, 'title', true);

        // Create form
        $form = $this->container->get('form.factory')
            ->create(new ImportFormType($sources));

        return $this->render('SampleCsvBundle:Upload:index.html.twig', array(
            'form'   => $form->createView(),
            'active' => self::$active
        ));
    }

    /**
     * Upload :: statusAction
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function statusAction(Request $request)
    {
        if ($request->getMethod() == 'POST') 
        {
            // Count of Acknowledges
            $count = $this->getDoctrine()
                ->getRepository('SampleCsvBundle:Files')
                ->findAcknowledge();

            // Upload Response
            $response = new JsonResponse(array('count' => $count));
            $response->headers->set('Vary', 'Accept');

            if (!in_array('application/json', $request->getAcceptableContentTypes())) {
                $response->headers->set('Content-type', 'text/plain');
            }

            return $response;
        }
    }
}
