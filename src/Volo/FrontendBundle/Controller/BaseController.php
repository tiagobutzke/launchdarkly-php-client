<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Volo\FrontendBundle\Security\Token;
use Volo\FrontendBundle\Service\CustomerService;

class BaseController extends Controller
{
    /**
     * @param array $data
     *
     * @return array
     */
    protected function sanitizeInputData($data)
    {
        array_walk($data, function(&$value) {
            $value = filter_var($value, FILTER_SANITIZE_STRING);
        });

        return $data;
    }

    /**
     * @param string $content
     *
     * @return array
     */
    protected function decodeJsonContent($content)
    {
        if ('' === $content) {
            throw new BadRequestHttpException('Content is empty.');
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException(
                sprintf('Content is not a valid json. Error message: "%s"', json_last_error_msg())
            );
        }

        return $data;
    }

    /**
     * @param int $customerId
     *
     * @throws AccessDeniedHttpException
     */
    protected function throwAccessDeniedIfCustomerNotAllowed($customerId)
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
     * @return CustomerService
     */
    protected function getCustomerService()
    {
        return $this->get('volo_frontend.service.customer');
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
