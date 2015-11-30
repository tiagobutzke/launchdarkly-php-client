<?php

namespace Volo\FrontendBundle\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonErrorResponse extends JsonResponse
{
    /**
     * Constructor.
     *
     * @param mixed $data The response data
     * @param int $status The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($data = null, $status = Response::HTTP_BAD_REQUEST, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
    }

}
