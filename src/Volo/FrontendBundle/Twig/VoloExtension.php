<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Volo\FrontendBundle\Service\CartManagerService;
use Symfony\Component\Intl\Intl;
use Volo\FrontendBundle\Service\ConfigurationService;

class VoloExtension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CartManagerService
     */
    private $cartManager;

    /**
     * @var ConfigurationService
     */
    private $config;

    /**
     * @param TranslatorInterface $translator
     * @param CartManagerService $cartManager
     * @param ConfigurationService $config
     */
    public function __construct(
        TranslatorInterface $translator,
        CartManagerService $cartManager,
        ConfigurationService $config
    ) {
        $this->translator = $translator;
        $this->cartManager = $cartManager;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array_merge(parent::getFilters(), [
            new \Twig_SimpleFilter('price', array($this, 'priceFilter')),
            new \Twig_SimpleFilter('formatTime', array($this, 'formatTime')),
            new \Twig_SimpleFilter('formatTimeFromString', array($this, 'formatTimeFromString')),
            new \Twig_SimpleFilter('dayOfTheWeek', array($this, 'formatDayOfTheWeek')),
            new \Twig_SimpleFilter('formatOpeningDay', array($this, 'formatOpeningDay')),
            new \Twig_SimpleFilter('prepareLogoUrl', array($this, 'prepareLogoUrl')),
            new \Twig_SimpleFilter('localisedDay', array($this, 'localisedDay')),
            new \Twig_SimpleFilter('languageName', array($this, 'getLanguageName')),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_default_cart_values', array($this, 'getDefaultCart')),
            new \Twig_SimpleFunction('get_default_cart_count', array($this, 'getDefaultCartCount')),
            new \Twig_SimpleFunction('get_default_cart_variations_ids', array($this, 'getDefaultCartVariationsIds')),
            new \Twig_SimpleFunction('get_default_cart_value', array($this, 'getDefaultCartValue')),
            new \Twig_SimpleFunction('get_default_cart_vendor_id', array($this, 'getDefaultCartVendorId')),
            new \Twig_SimpleFunction('get_currency_symbol_iso', array($this, 'getCurrencySymbolIso')),
            new \Twig_SimpleFunction('get_currency_symbol', array($this, 'getCurrencySymbol')),
            new \Twig_SimpleFunction('get_minimum_order_value_setting', array($this, 'getMinimumOrderValueSetting')),

            new \Twig_SimpleFunction('gtm_delivery_day', array($this, 'createDeliveryDay')),
            new \Twig_SimpleFunction('gtm_delivery_weekday', array($this, 'createDeliveryWeekday')),
        ];
    }

    /**
     * @return string
     */
    public function getCurrencySymbolIso()
    {
        return $this->config->getConfiguration()->getCurrencySymbolIso();
    }

    /**
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->config->getConfiguration()->getCurrencySymbol();
    }


    /**
     * @return string
     */
    public function getMinimumOrderValueSetting()
    {
        return $this->config->getConfiguration()->getMinimumOrderValue();
    }

    /**
     * @param float $number
     *
     * @return string
     * @throws \Twig_Error_Syntax
     */
    public function priceFilter($number)
    {
        $formatter = twig_get_number_formatter($this->translator->getLocale(), 'currency');

        return $formatter->formatCurrency($number, $this->getCurrencySymbolIso());
    }

    /**
     * @param SessionInterface $session
     *
     * @return int
     */
    public function getDefaultCartCount(SessionInterface $session)
    {
        $cart = $this->cartManager->getDefaultCart($session);

        return $cart === null ? 0 : array_sum(array_column($cart['products'], 'quantity'));
    }

    /**
     * @param SessionInterface $session
     *
     * @return array
     */
    public function getDefaultCart(SessionInterface $session)
    {
        $cart = $this->cartManager->getDefaultCart($session);

        return [
            'vendor_id'      => empty($cart['vendor_id']) ? '' : $cart['vendor_id'],
            'products_count' => empty($cart['products']) ? 0 : array_sum(array_column($cart['products'], 'quantity')),
        ];
    }

    /**
     * @param SessionInterface $session
     *
     * @return string
     */
    public function getDefaultCartVariationsIds(SessionInterface $session)
    {
        $cart = $this->cartManager->getDefaultCart($session);

        if ($cart !== null) {
            $ids = array_column($cart['products'], 'variation_id');

            return implode(',', $ids);
        }

        return '';
    }

    /**
     * @param SessionInterface $session
     *
     * @return float
     */
    public function getDefaultCartValue(SessionInterface $session)
    {
        $cart = $this->cartManager->getDefaultCart($session);

        if ($cart !== null) {
            try {
                return $this->cartManager->calculateCart($cart)['total_value'];
            } catch (ApiErrorException $exception) {
                // do nothing, we'll return 0 anyway.
            }
        }

        return .0;
    }

    /**
     * @param SessionInterface $session
     *
     * @return string
     */
    public function getDefaultCartVendorId(SessionInterface $session)
    {
        $cart = $this->cartManager->getDefaultCart($session);

        return ($cart === null || !array_key_exists('vendor_id', $cart)) ? '' : $cart['vendor_id'];
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

    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function formatTime(\DateTime $dateTime)
    {
        $formatter = $this->getFormatter();

        return $formatter->format($dateTime);
    }

    /**
     * @param string $dateTime
     *
     * @return string
     */
    public function formatTimeFromString($dateTime)
    {
        $date = new \DateTime($dateTime);

        return $this->formatTime($date);
    }

    /**
     * @param int $dayIndex
     *
     * @return string
     */
    public function formatDayOfTheWeek($dayIndex)
    {
        $timestamp = strtotime("+{$dayIndex} day", strtotime('next Sunday'));
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);
        $formatter = \IntlDateFormatter::create($this->translator->getLocale(), \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);

        // @see http://userguide.icu-project.org/formatparse/datetime for formats
        $formatter->setPattern('eee');

        return $formatter->format($dateTime);
    }

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    public function localisedDay(\DateTime $date)
    {
        $formatter = \IntlDateFormatter::create($this->translator->getLocale(), \IntlDateFormatter::GREGORIAN, \IntlDateFormatter::NONE);

        $formatter->setPattern('eeee');
        return $formatter->format($date);
    }

    /**
     * @param string $logoUrl
     * @param array $dimensions [w, h]
     *
     * @return string
     */
    public function prepareLogoUrl($logoUrl, $dimensions)
    {
        $logoUrl = preg_replace('/^http:/', 'https:', $logoUrl);

        return sprintf($logoUrl, $dimensions[0], $dimensions[1]);
    }

    // ---------------------------------- @todo to be extended to a separate extension

    /**
     * @param string $time
     *
     * @return string
     */
    public function createDeliveryWeekday($time)
    {
        $dateTime = new \DateTime($time);

        return $dateTime->format('D');
    }

    /**
     * @param string $orderTime
     * @param string $orderConfirmedDeliveryTime
     *
     * @return string
     */
    public function createDeliveryDay($orderTime, $orderConfirmedDeliveryTime)
    {
        $timeDifference = strtotime($orderConfirmedDeliveryTime) - strtotime($orderTime);

        return floor($timeDifference / 86400);
    }

    /**
     * @param string $locale
     *
     * @return string
     *
     */
    public function getLanguageName($locale)
    {
        return Intl::getLanguageBundle()->getLanguageName(substr($locale, 0, 2), null, $locale);
    }

    /**
     * @return \IntlDateFormatter
     */
    private function getFormatter()
    {
        $formatter = \IntlDateFormatter::create($this->translator->getLocale(), \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT);

        return $formatter;
    }
}
