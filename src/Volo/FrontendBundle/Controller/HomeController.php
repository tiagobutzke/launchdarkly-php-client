<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function homeAction()
    {
        return [
            'cities' => $this->get('volo_frontend.provider.city')->findAll()->getItems()
        ];
    }

    /**
     * @Route("/city/{id}", name="city")
     * @Template()
     */
    public function cityAction($id)
    {
        $city = $this->get('volo_frontend.provider.city')->find($id);

        $areas = $this->get('volo_frontend.provider.area')->findByCity($id);

        $vendors = $this->get('volo_frontend.provider.vendor')->findVendorsByArea($areas->getItems()->first());
        
        return [
            'city' => $city,
            'vendors' => $vendors->getItems()
        ];
    }
}
