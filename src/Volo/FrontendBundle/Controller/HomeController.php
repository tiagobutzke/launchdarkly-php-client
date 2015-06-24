<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home", defaults={"postalCode": ""})
     * @Route("/site/index/code/{code}", name="home_with_change_password_modal", defaults={"postalCode": ""})
     * @Route("/filter/{postalCode}", name="filter_postalCode")
     * @Template()
     *
     * @param string $postalCode
     * @param string $_route
     *
     * @return array
     */
    public function homeAction($postalCode, $_route)
    {
        return [
            'showChangePasswordModal' => $_route === 'home_with_change_password_modal',
            'postalCode'              => $postalCode,
        ];
    }
}
