<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
     * @return CustomerService
     */
    protected function getCustomerService()
    {
        return $this->get('volo_frontend.service.customer');
    }
}
