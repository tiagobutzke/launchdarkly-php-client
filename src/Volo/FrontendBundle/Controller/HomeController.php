<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home", defaults={"postalCode": ""})
     * @Route("/filter/{postalCode}", name="filter_postalCode")
     * @Template()
     *
     * @param $postalCode
     *
     * @return array
     */
    public function homeAction($postalCode)
    {
        return [
            'postalCode' => $postalCode
        ];
    }
}
