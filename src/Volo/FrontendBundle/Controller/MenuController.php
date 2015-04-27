<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MenuController extends Controller
{
    /**
     * @Route("/menu", name="menu")
     * @Template()
     */
    public function menuAction()
    {
        return [];
    }
}
