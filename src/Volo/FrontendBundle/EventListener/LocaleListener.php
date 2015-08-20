<?php

namespace Volo\FrontendBundle\EventListener;

use Foodpanda\ApiSdk\Api\FoodpandaClient;
use Foodpanda\ApiSdk\Entity\Language\Language;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;
use Volo\FrontendBundle\Provider\LocaleConfigProvider;
use Volo\FrontendBundle\Service\ConfigurationService;
use Volo\FrontendBundle\Service\TranslatorService;

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
     * @var LocaleConfigProvider
     */
    protected $localeConfigProvider;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @param ConfigurationService $config
     * @param FoodpandaClient $client
     * @param LocaleConfigProvider $localeConfigProvider
     * @param TranslatorService $translator
     * @param Router $router
     *
     */
    public function __construct(
        ConfigurationService $config,
        FoodpandaClient $client,
        LocaleConfigProvider $localeConfigProvider,
        TranslatorService $translator,
        Router $router
    ) {
        $this->client = $client;
        $this->config = $config;
        $this->localeConfigProvider = $localeConfigProvider;
        $this->router = $router;
        $translator->setLocaleConfigProvider($localeConfigProvider);
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $detectedBrowserLocale = $this->detectBrowserLocale($request);
        $defaultLocale = $request->getDefaultLocale();
        if ($detectedBrowserLocale !== null && $detectedBrowserLocale !== $defaultLocale) {
            $this->redirectToLocalizedUrl($event, $request, $detectedBrowserLocale);

            return;
        }

        $languageId = $this->getLanguageId($request->getLocale());
        if (null !== $languageId) {
            $this->client->setLanguageId($languageId);
        }
    }

    /**
     * @param Request $request
     *
     * @throws \InvalidArgumentException
     * @return null|string
     */
    protected function detectBrowserLocale(Request $request)
    {
        $hasSession = $request->hasPreviousSession();
        $browserLangDetectionEnabled = $this->localeConfigProvider->isBrowserLanguageDetectionEnabled();
        $localeAttribute = $request->attributes->get('locale', false);
        if (!$hasSession && !$localeAttribute && $browserLangDetectionEnabled) {
            $preferredLanguage = $request->getPreferredLanguage($this->localeConfigProvider->getSupportedLocales());
            if (false !== $preferredLanguage && false === strpos($request->getPathInfo(), '/' . $preferredLanguage)) {
                return $preferredLanguage;
            }
        }

        return null;
    }

    /**
     * @param GetResponseEvent $event
     * @param Request $request
     * @param string $detectedBrowserLocale
     *
     * @throws \InvalidArgumentException
     */
    public function redirectToLocalizedUrl(GetResponseEvent $event, Request $request, $detectedBrowserLocale)
    {
        $pathInfo = $request->getPathInfo();
        $url = $request->getBaseUrl() . '/' . $detectedBrowserLocale . $pathInfo;

        $event->setResponse(new RedirectResponse($url));
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
