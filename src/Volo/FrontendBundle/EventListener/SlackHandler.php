<?php

namespace Volo\FrontendBundle\EventListener;

use Monolog\Handler\SlackHandler as BaseSlackHandler;

class SlackHandler extends BaseSlackHandler
{
    use MonologHandlerTrait;

    /**
     * {@inheritdoc}
     *
     * @param array $record
     */
    protected function write(array $record)
    {
        if ($this->isAllowedToBeWritten($record)) {
            parent::write($record);
        }
    }
}
