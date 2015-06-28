<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\WebTranslateIt\Model\StringFilter;
use Foodpanda\WebTranslateIt\Model\StringParameters;
use Foodpanda\WebTranslateIt\Services\TranslationService;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class WTIService
{
    /**
     * @var TranslationService
     */
    private $wtiClient;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param TranslationService $wtiClient
     * @param FileLocator $fileLocator
     * @param $bundleName
     */
    public function __construct(TranslationService $wtiClient, FileLocator $fileLocator, $bundleName)
    {
        $this->wtiClient = $wtiClient;
        $this->rootDir = $fileLocator->locate($bundleName. '/Resources/translations');
        if (is_array($this->rootDir)) {
            throw new \InvalidArgumentException(sprintf('Found more than one path: "%s"', var_export($this->rootDir, true)));
        }
    }

    /**
     * @return MessageCatalogue[]
     */
    public function sync()
    {
        $catalogues = $this->getCatalogues();

        $strings = $this->getStrings($catalogues);

        $this->populateCatalogues($catalogues, $strings);

        $this->dumpCatalogues($catalogues, $this->getDefaultLocale());

        return $catalogues;
    }

    /**
     * @return MessageCatalogue[]
     */
    private function getCatalogues()
    {
        $projects = $this->wtiClient->getProjectInformation();

        /** @var MessageCatalogue[] $catalogues */
        $catalogues = [];

        $locales = $projects['project']['target_locales'];
        foreach ($locales as $language) {
            $locale = $language['code'];
            $catalogues[$locale] = new MessageCatalogue($locale);
        }
        ksort($catalogues);

        return $catalogues;
    }

    /**
     * @return string
     */
    private function getDefaultLocale()
    {
        $projects = $this->wtiClient->getProjectInformation();

        return $projects['project']['source_locale']['code'];
    }

    /**
     * @param MessageCatalogue[] $catalogues
     *
     * @return array
     */
    private function getStrings(array $catalogues)
    {
        $filter = new StringFilter();
        $filter->setLabels([]);
        $params = new StringParameters($filter);
        $params->setLocales(array_keys($catalogues));

        return $this->wtiClient->getStrings($params);
    }

    /**
     * @param MessageCatalogue[] $catalogues
     * @param string $defaultLocale
     */
    private function dumpCatalogues(array $catalogues, $defaultLocale)
    {
        $dumper = new XliffFileDumper();
        $dumper->setBackup(false);
        foreach ($catalogues as $catalogue) {
            $dumper->dump($catalogue, ['path' => $this->rootDir, 'default_locale' => $defaultLocale]);
        }
    }

    /**
     * @param MessageCatalogue[] $catalogues
     * @param array $strings
     */
    private function populateCatalogues($catalogues, array $strings)
    {
        foreach ($strings as $string) {
            $key = $string['key'];
            foreach ($string['translations'] as $translation) {
                $locale = $translation['locale'];
                if ($string['plural']) {
                    $text = implode('|', $translation['text']);
                } else {
                    $text = $translation['text'];
                }
                $catalogues[$locale]->set($key, $text);
            }

        }
    }
}
