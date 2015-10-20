<?php

namespace Volo\FrontendBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Foodpanda\ApiSdk\Entity\City\City;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Volo\FrontendBundle\Exception\CityNotFoundException;
use Volo\FrontendBundle\Service\CityService;

class CityLocationConverter implements ParamConverterInterface
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
        $cityUrlKey = $request->attributes->get('cityUrlKey', false);

        if (!$cityUrlKey) {
            $message = $this->translator->trans('customer.location.missing_keys');

            throw new BadRequestHttpException($message);
        }
        try {
            $city = $this->cityService->findCityByCode($cityUrlKey);
        } catch(CityNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        $request->attributes->set('city', $city);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === City::class;
    }
}
