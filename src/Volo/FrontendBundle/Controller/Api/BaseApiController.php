<?php

namespace Volo\FrontendBundle\Controller\Api;

use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Serializer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Volo\FrontendBundle\Controller\BaseController;
use Volo\FrontendBundle\Security\Token;

class BaseApiController extends BaseController
{
    /**
     * @param int $customerId
     *
     * @throws AccessDeniedHttpException
     */
    protected function isCustomerAllowed($customerId)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedHttpException('Access denied.');
        }

        /** @var Customer $customer */
        $customer = $this->getToken()->getAttribute('customer');
        if ($customerId !== $customer->getId()) {
            throw new AccessDeniedHttpException(sprintf('Customer id %d is not allowed.', $customerId));
        }
    }

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        return $this->get('volo_frontend.api.serializer');
    }

    /**
     * @return Token
     */
    protected function getToken()
    {
        return $this->get('security.token_storage')->getToken();
    }
}
