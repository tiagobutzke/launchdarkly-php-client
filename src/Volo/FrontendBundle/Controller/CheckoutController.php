<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CheckoutController extends Controller
{
    /**
     * @Route("/checkout/{vendorCode}", name="checkout")
     * @Template()
     */
    public function indexAction($vendorCode)
    {
        
    }
}
