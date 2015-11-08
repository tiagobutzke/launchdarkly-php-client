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

    /**
     * Prepares content data
     *
     * @param  array $record
     *
     * @return array
     */
    protected function prepareContentData($record)
    {
        $data = parent::prepareContentData($record);
        $attachments = json_decode($data['attachments'], true);

        $prefix = sprintf('%s :flag-%s:', strtoupper($data['username']), $data['username']);
        foreach ($attachments as &$attachment) {
            if (isset($attachment['fields'][0]['title'])) {
                $attachment['fields'][0]['title'] = $attachment['fields'][0]['title'] . ' ' . $prefix;
            }
        }
        $data['attachments'] = json_encode($attachments);

        return $data;
    }

}
