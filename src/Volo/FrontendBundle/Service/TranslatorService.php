<?php

namespace Volo\FrontendBundle\Service;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Volo\FrontendBundle\Provider\LocaleConfigProvider;

class TranslatorService extends Translator
{
    /**
     * @var LocaleConfigProvider
     */
    protected $localeConfigProvider;

    /**
     * @param LocaleConfigProvider $localeConfigProvider
     */
    public function setLocaleConfigProvider($localeConfigProvider)
    {
        $this->localeConfigProvider = $localeConfigProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function setLocale($locale)
    {
        if ($this->localeConfigProvider !== null) {
            $locale = $this->localeConfigProvider->getFullLocale($locale);
        }
        parent::setLocale($locale);
    }
}
