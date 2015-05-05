<?php

namespace Foodpanda\ApiSdk\Exception;

use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;

class ApiErrorException extends ApiException
{
    /**
     * @var string
     */
    protected $jsonErrorMessage;

    /**
     * @param string $message
     * @param string $jsonErrorMessage
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param \Exception $previous
     */
    public function __construct(
        $message,
        $jsonErrorMessage,
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $previous = null
    ) {
        parent::__construct($message, $request, $response, $previous);

        $this->jsonErrorMessage = $jsonErrorMessage;
    }

    /**
     * @return string
     */
    public function getJsonErrorMessage()
    {
        return $this->jsonErrorMessage;
    }
}
