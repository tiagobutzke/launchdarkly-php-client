<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Foodpanda\ApiSdk\Exception\ApiException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Volo\FrontendBundle\Http\JsonErrorResponse;

class ApiErrorTranslator
{
    const UNKNOWN_PAYMENT_ERROR_MESSAGE = 'payment.unknown_error';

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param TranslatorInterface $translator
     * @param LoggerInterface $logger
     */
    public function __construct(TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @param ApiException $exception
     *
     * @return JsonErrorResponse
     */
    public function createJsonErrorResponse(ApiException $exception)
    {
        if ($exception instanceof ApiErrorException) {
            $response =$this->createResponseforApiErrorException($exception);
        } elseif ($exception instanceof ApiException) {
            $response = $this->createResponseForUnknownError($exception);
        } else {
            $response = $this->createResponseForUnknownError($exception);
        }

        return $response;
    }

    /**
     * @param \Exception $exception
     *
     * @return JsonErrorResponse
     */
    public function createUnknownErrorJsonResponse(\Exception $exception)
    {
        return $this->createResponseForUnknownError($exception);
    }

    /**
     * @param ApiErrorException $exception
     *
     * @return JsonErrorResponse
     */
    private function createResponseForApiErrorException(ApiErrorException $exception)
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
            $exceptionType = $errors['exception_type'];
            $customerExists = ['ApiCustomerAlreadyExistsException', 'ApiFacebookCustomerAlreadyExistsException'];

            if (in_array($exceptionType, $customerExists, true)) {
                $exceptionType = 'ApiCustomerAlreadyExistsException';
            }

            $errors['message'] = $this->translator->trans($exceptionType);
            if ($errors['message'] === $errors['exception_type']) {
                $errors['message'] = $this->translator->trans(self::UNKNOWN_PAYMENT_ERROR_MESSAGE);
            }
        }

        $data = [
            'error' => [
                'code' => $status,
                'errors' => $errors,
            ],
        ];

        return new JsonErrorResponse($data, $status);
    }

    /**
     * @param \Exception $exception
     *
     * @return JsonErrorResponse
     */
    private function createResponseForUnknownError(\Exception $exception)
    {
        $status = Response::HTTP_BAD_REQUEST;
        $exceptionType = (new \ReflectionClass($exception))->getShortName();
        $developerMessage = $exception->getMessage();

        $message = $this->translator->trans($exceptionType);
        if ($message === $exceptionType) {
            $message = $this->translator->trans(self::UNKNOWN_PAYMENT_ERROR_MESSAGE);
        }

        $data = [
            'error' => [
                'code' => $status,
                'errors' => [
                    'exception_type' => get_class($exception),
                    'message' => $message,
                    'developer_message' => $developerMessage,
                    'more_information' => null,
                ],
            ],
        ];

        $this->logger->error($developerMessage, array(
            'exception' => $exception,
            'stack_trace' => $exception->getTraceAsString(),
            'json_response' => $data
        ));

        return new JsonErrorResponse($data, $status);
    }
}
