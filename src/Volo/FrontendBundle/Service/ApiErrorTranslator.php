<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Volo\FrontendBundle\Http\JsonErrorResponse;

class ApiErrorTranslator
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param ApiErrorException $exception
     *
     * @return JsonErrorResponse
     */
    public function createTranslatedJsonResponse(ApiErrorException $exception)
    {
        $errorMessages = json_decode($exception->getJsonErrorMessage(), true);
        $status = Response::HTTP_BAD_REQUEST;

        // TODO: need to make it nicer
        if (isset($errorMessages['data']['items'])) {
            $errors = $errorMessages['data']['items'];
        } else {
            $errors = $errorMessages['data'];
            $status = Response::HTTP_PRECONDITION_FAILED;
        }

        if (isset($errors['exception_type'])) {
            $errors['message'] = $this->translator->trans($errors['exception_type']);
        }

        $data = [
            'error' => [
                'code' => $status,
                'errors' => $errors,
            ],
        ];

        
        return new JsonErrorResponse($data, $status);
    }
}
