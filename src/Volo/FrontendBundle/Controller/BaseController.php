<?php

namespace Volo\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @param Request $request
     *
     * @return array
     */
    protected function decodeJsonContent(Request $request)
    {
        $content = $request->getContent();

        if ('' === $content) {
            throw new BadRequestHttpException('Content is empty.');
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Content is not a valid json.');
        }

        return $data;
    }
}
