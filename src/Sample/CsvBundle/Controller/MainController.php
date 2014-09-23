<?php

namespace Sample\CsvBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * Main
     * 
     * @return redirect
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('graph'));
    }
}
