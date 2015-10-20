<?php

namespace Volo\FrontendBundle\Request\ParamConverter;

use Foodpanda\ApiSdk\Entity\Cart\LocationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Foodpanda\ApiSdk\Entity\Cart\CityLocation;
use Foodpanda\ApiSdk\Entity\Cart\GpsLocation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Volo\FrontendBundle\Exception\CityNotFoundException;
use Volo\FrontendBundle\Service\CityService;

class UserLocationConverter implements ParamConverterInterface
{
    /**
     * CityService
     */
    protected $cityService;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param CityService $cityService
     * @param Translator $translator
     */
    public function __construct(CityService $cityService, Translator $translator)
    {
        $this->cityService = $cityService;
        $this->translator = $translator;
    }

    /**
     * @inheritdoc
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $cityId = $request->get('city_id', false);
        $cityUrlKey = $request->get('cityUrlKey', false);
        $lat = $request->get('latitude', false);
        $lng = $request->get('longitude', false);

        switch(true) {
            case $cityUrlKey:
                try {
                    $city = $this->cityService->findCityByCode($cityUrlKey);
                } catch (CityNotFoundException $e) {
                    throw new NotFoundHttpException($e->getMessage());
                }
                $location = new CityLocation($city->getId());

                break;
            case $cityId:
                try {
                    $city = $this->cityService->findCityById($cityId);
                } catch (CityNotFoundException $e) {
                    throw new NotFoundHttpException($e->getMessage());
                }
                $location = new CityLocation($city->getId());

                break;
            case is_numeric($lat) && is_numeric($lng):
                $location = new GpsLocation($lat, $lng);

                break;
            default:
                $message = $this->translator->trans('customer.location.missing_keys');

                throw new BadRequestHttpException($message);
        }

        $request->attributes->set('location', $location);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return is_subclass_of($configuration->getClass(), LocationInterface::class);
    }
}
