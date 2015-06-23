<?php

namespace Volo\FrontendBundle\Service\Provider;

use Doctrine\Common\Cache\PhpFileCache;

class TranslatorCache extends PhpFileCache
{
    public function fetch($id)
    {
        $catalog = parent::fetch($id);

        if (is_array($catalog) && strpos($id, 'translator') !== 0) {
            $catalog = $this->buildTranslationKey($id, $catalog);
        }

        return $catalog;
    }

    /**
     * @param string $id
     * @param array $catalog
     *
     * @return string
     */
    protected function buildTranslationKey($id, array $catalog)
    {
        foreach($catalog as $section => $values) {
            foreach($values as $key => $value) {
                // an attempt to build the key for a non-plural
                if (!is_array($value)) {
                    $catalog[$section][$key] = $this->normalizeToken($value);
                    continue;
                }

                /*
                 * Creating a special token for translator. E.g.
                 * - %count% restaurant|%count% restaurants
                 * - %count% бекон|%count% бекона|%count% беконов
                 *
                 * {{count}} is hardcoded Webtranslate's token. %count% is hardcoded translator's token.
                 */
                $translationString = '';
                foreach (['one', 'few', 'many', 'other'] as $option) {
                    if (isset($value[$option])) {
                        $translationString .= $value[$option] . '|';
                    }
                }

                $catalog[$section][$key] = $this->normalizeToken(rtrim($translationString, '|'));
            }
        }

        return $catalog;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function normalizeToken($string)
    {
        return str_replace('{{count}}', '%count%', $string);
    }
}
