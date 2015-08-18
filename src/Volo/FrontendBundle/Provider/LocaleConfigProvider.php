<?php

namespace Volo\FrontendBundle\Provider;

class LocaleConfigProvider
{
    /**
     * array
     */
    protected $config;

    /**
     * @param array $localConfig
     */
    public function __construct(array $localConfig)
    {
        $this->config = $localConfig;
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    public function getFullLocale($locale)
    {
        if (array_key_exists($locale, $this->config['localeMapping'])) {
            $locale = $this->config['localeMapping'][$locale];
        }

        return $locale;
    }

    /**
     * @return bool
     */
    public function isBrowserLanguageDetectionEnabled()
    {
        return (bool)$this->config['detectBrowserLanguage'];
    }

    /**
     * @return array
     */
    public function getSupportedLocales()
    {
        return $this->config['locales'];
    }

    /**
     * @return array
     */
    public function getDefaultLocale()
    {
        return $this->config['defaultLocale'];
    }
}
