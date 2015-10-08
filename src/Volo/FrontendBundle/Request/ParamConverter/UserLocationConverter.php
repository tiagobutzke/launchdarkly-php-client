<?php

namespace Volo\FrontendBundle\Request\ParamConverter;

use Foodpanda\ApiSdk\Entity\Cart\AbstractLocation;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Foodpanda\ApiSdk\Entity\Cart\CityLocation;
use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Foodpanda\ApiSdk\Entity\Cart\AreaLocation;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserLocationConverter implements ParamConverterInterface
{
    /**
     * CityProvider
     */
    protected $cityProvider;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param CityProvider $cityProvider
     * @param Translator $translator
     */
    public function __construct(CityProvider $cityProvider, Translator $translator)
    {
        $this->cityProvider = $cityProvider;
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $convertedParameter = null;

        $cityId = $request->get('city_id', false);
        $cityUrlKey = $request->get('cityUrlKey', false);
        $areaId = $request->get('area_id', false);
        $lat = $request->get('latitude', false);
        $lng = $request->get('longitude', false);

        switch(true) {
            case $cityUrlKey:
                $city = $this->findCityByCode($cityUrlKey);
                if ($city === false) {
                    throw new NotFoundHttpException(sprintf('City with the url key "%s" is not found', $cityUrlKey));
                }
                $request->attributes->set('cityObject', $city);

                $cityId = $city->getId();
            case $cityId:
                $convertedParameter = new CityLocation($cityId);

                break;
            case $areaId:
                $convertedParameter = new AreaLocation($areaId);

                break;
            case is_numeric($lat) && is_numeric($lng):
                $convertedParameter = new GpsLocation($lat, $lng);

                break;
            default:
                $message = $this->translator->trans('customer.location.missing_keys');
                throw new BadRequestHttpException($message);
        }

        $request->attributes->set('location', $convertedParameter);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === AbstractLocation::class;
    }

    /**
     * @param string $cityUrlKey
     *
     * @return City
     */
    protected function findCityByCode($cityUrlKey)
    {
        $filtered = $this->cityProvider->findAll()->getItems()->filter(function($element) use ($cityUrlKey) {
            /** @var City $element */
            return strtolower($element->getUrlKey()) === strtolower($cityUrlKey);
        });

        return $filtered->first();
    }
}
