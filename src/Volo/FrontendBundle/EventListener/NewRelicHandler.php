<?php

namespace Volo\FrontendBundle\EventListener;

use GuzzleHttp\Exception\ClientException;
use Monolog\Handler\NewRelicHandler as BaseNewRelicHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewRelicHandler extends BaseNewRelicHandler
{
    /**
     * @var string[]
     */
    protected $exceptionClassNamesBlackList = [
        ClientException::class,
        NotFoundHttpException::class,
    ];

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        $isAllowedToBeWritten = true;

        if (isset($record['context']['exception'])) {
            foreach ($this->exceptionClassNamesBlackList as $blackListExceptionClassName) {
                if ($record['context']['exception'] instanceof $blackListExceptionClassName) {
                    $isAllowedToBeWritten = false;
                    break;
                }
            }
        }

        if ($isAllowedToBeWritten) {
            parent::write($this->addPreviousExceptions($record));
        }
    }

    /**
     * New Relic seems to not support chained exceptions, add some information to "extra" fields
     *
     * @param array $record
     * @return array
     */
    private function addPreviousExceptions(array $record)
    {
        if (isset($record['context']['exception']) && $record['context']['exception'] instanceof \Exception) {
            /** @var \Exception $current */
            $counter = 1;
            $current = $record['context']['exception'];
            while ($current = $current->getPrevious()) {
                $key = sprintf("#%d: %s", $counter++, get_class($current));
                $value = sprintf('"%s" %s (%d)', $current->getMessage(), $current->getFile(), $current->getLine());
                $record['extra'][$key] = $value;
            }
        }

        return $record;
    }
}
