<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use Foodpanda\ApiSdk\Provider\CmsProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Volo\FrontendBundle\Service\CustomerLocationService;

class CustomerLocationExtension extends \Twig_Extension
{
    /**
     * @var CustomerLocationService
     */
    protected $customerLocationService;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(CustomerLocationService $customerLocationService, RequestStack $requestStack)
    {
        $this->customerLocationService = $customerLocationService;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('customer_location', [$this, 'getCustomerLocation'], ['is_safe' => ['html']]),
        );
    }

    public function getCustomerLocation() {
        return $this->customerLocationService->get($this->request->getSession());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'volo_frontend.customer_location_extension';
    }
}
