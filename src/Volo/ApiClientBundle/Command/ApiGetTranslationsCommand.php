<?php

namespace Volo\ApiClientBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Foodpanda\ApiSdk\Api\TranslationApiClient;

class ApiGetTranslationsCommand extends AbstractApiClientCommand
{
    /**
     * @return string
     */
    protected function getCommandName()
    {
        return 'api:translation:translations';
    }

    /**
     * @return string
     */
    protected function getCommandDescription()
    {
        return 'Display a list of translations';
    }

    /**
     * @return TranslationApiClient
     */
    protected function getClientApi()
    {
        return $this->getContainer()->get('volo_frontend.api.translation_api_client');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeApiCall(InputInterface $input)
    {
        return $this->getClientApi()->getTranslations();
    }
}
