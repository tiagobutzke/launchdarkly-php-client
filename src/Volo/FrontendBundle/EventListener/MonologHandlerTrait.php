<?php

namespace Volo\FrontendBundle\EventListener;

use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait MonologHandlerTrait
{
    /**
     * @var string[]
     */
    protected $exceptionClassNamesBlackList = [
        ClientException::class,
        NotFoundHttpException::class,
    ];

    /**
     * @param array $record
     *
     * @return bool
     */
    protected function isAllowedToBeWritten(array $record)
    {
        if (isset($record['context']['exception'])) {
            foreach ($this->exceptionClassNamesBlackList as $blackListExceptionClassName) {
                if ($record['context']['exception'] instanceof $blackListExceptionClassName) {
                    return false;
                }
            }
        }

        return true;
    }

}
