<?php

namespace Volo\FrontendBundle\EventListener;

use Foodpanda\ApiSdk\Api\FoodpandaClient;
use Foodpanda\ApiSdk\Entity\Language\Language;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Volo\FrontendBundle\Service\ConfigurationService;

class LocaleListener
{
    /**
     * @var ConfigurationService
     */
    protected $config;

    /**
     * @var FoodpandaClient
     */
    protected $client;

    /**
     * @param ConfigurationService $config
     * @param FoodpandaClient $client
     */
    public function __construct(ConfigurationService $config, FoodpandaClient $client)
    {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $languageId = $this->getLanguageId($event->getRequest()->getLocale());
        if (null !== $languageId) {
            $this->client->setLanguageId($languageId);
        }
    }

    /**
     * @param string $locale
     *
     * @return int
     */
    protected function getLanguageId($locale)
    {
        $languages = $this->config->getConfiguration()->getLanguages();
        /** @var Language $language */
        foreach ($languages as $language) {
            $languageShortName = explode('_', $language->getLanguageCode())[0];
            if (in_array($locale, [$languageShortName, $language->getLanguageCode()], true)) {
                return $language->getLanguageId();
            }
        }

        return null;
    }
}
