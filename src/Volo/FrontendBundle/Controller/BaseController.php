<?php

namespace Volo\FrontendBundle\Controller;

use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Volo\FrontendBundle\Provider\AddressConfigProvider;
use Volo\FrontendBundle\Security\Token;
use Volo\FrontendBundle\Service\CityService;
use Volo\FrontendBundle\Service\CustomerLocationService;
use Volo\FrontendBundle\Service\CustomerService;
use Volo\FrontendBundle\Service\VendorService;

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
     * @return VendorService
     */
    protected function getVendorService()
    {
        return $this->get('volo_frontend.service.vendor');
    }

    /**
     * @return CityService
     */
    protected function getCityService()
    {
        return $this->get('volo_frontend.service.city');
    }

    /**
     * @return CustomerLocationService
     */
    protected function getCustomerLocationService()
    {
        return $this->get('volo_frontend.service.customer_location');
    }

    /**
     * @return AddressConfigProvider
     */
    protected function getAddressConfigProvider()
    {
        return $this->get('volo_frontend.provider.address_config_provider');
    }

    /**
     * @return Token
     */
    protected function getToken()
    {
        return $this->get('security.token_storage')->getToken();
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters with an absolute URL.
     *
     * @param string $route The name of the route
     * @param array $parameters An array of parameters
     * @param int $status The status code to use for the Response
     *
     * @return RedirectResponse
     */
    protected function redirectToRoute($route, array $parameters = array(), $status = 302)
    {
        return $this->redirect($this->generateUrl($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL), $status);
    }
}
