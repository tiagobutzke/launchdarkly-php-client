<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class VendorController extends Controller
{
    /**
     * @Route("/vendor/{id}", name="vendor")
     * @Template()
     */
    public function vendorAction($id)
    {
        return [
            'vendor' => $this->get('volo_frontend.provider.vendor')->find($id),
        ];
    }
}
