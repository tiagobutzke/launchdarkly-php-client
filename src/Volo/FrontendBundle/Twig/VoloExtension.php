<?php

namespace Volo\FrontendBundle\Twig;

use Twig_Extensions_Extension_Intl;

class VoloExtension extends Twig_Extensions_Extension_Intl
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @param string $locale
     */
    public function __construct($locale)
    {
        parent::__construct();

        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array_merge(parent::getFilters(), [
            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_configuration', array($this, 'getConfiguration')),
        ];
    }

    /**
     * @param float $number
     *
     * @return string
     * @throws \Twig_Error_Syntax
     */
    public function priceFilter($number)
    {
        $formatter = twig_get_number_formatter($this->locale, 'currency');
        $currency = $formatter->getTextAttribute(\NumberFormatter::CURRENCY_CODE);

        return $formatter->formatCurrency($number, $currency);
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        $formatter = twig_get_number_formatter($this->locale, 'currency');
        $currencyIso = $formatter->getTextAttribute(\NumberFormatter::CURRENCY_CODE);

        return [
            'currency' => [
                'currency_symbol_iso' => $currencyIso,
            ]
        ];
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'volo_frontend.twig_extension';
    }
}
