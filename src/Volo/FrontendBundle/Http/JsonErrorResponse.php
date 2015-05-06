<?php

namespace Volo\FrontendBundle\Http;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonErrorResponse extends JsonResponse
{
    /**
     * @param ApiErrorException $exception
     * @param int $status
     * @param array $headers
     */
    public function __construct(
        ApiErrorException $exception,
        $status = Response::HTTP_BAD_REQUEST,
        $headers = array()
    ) {
        $errorMessages = json_decode($exception->getJsonErrorMessage(), true);

        // TODO: need to make it nicer
        if (isset($errorMessages['data']['items'])) {
            $errors = $errorMessages['data']['items'];
        } else {
            $errors = $errorMessages['data'];
            $status = Response::HTTP_PRECONDITION_FAILED;
        }

        $data = [
            'error' => [
                'code' => $status,
                'errors' => $errors,
            ],
        ];

        parent::__construct($data, $status, $headers);
    }
}
