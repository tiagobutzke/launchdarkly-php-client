<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class RestaurantsController extends Controller
{
    /**
     * @Route("/restaurants", name="restaurants")
     * @Template()
     */
    public function restaurantsAction()
    {
        return [];
    }
}
