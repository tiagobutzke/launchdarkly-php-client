<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class VendorController extends Controller
{
    /**
     * @Route(
     *      "/restaurant/{code}/{slug}",
     *      name="vendor",
     *      requirements={
     *          "code": "([A-Za-z][A-Za-z0-9]{3})"
     *      }
     * )
     * @Template()
     * @Method({"GET"})
     *
     * @param string $code
     * @return array
     */
    public function vendorAction($code)
    {
        $entity = null;
        try {
            $entity = $this->get('volo_frontend.provider.vendor')->find($code);
        } catch (EntityNotFoundException $exception) {
            throw $this->createNotFoundException('Vendor not found!', $exception);
        }

        return ['vendor' => $entity];
    }
}
