<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Api\CmsApiClient;
use Foodpanda\ApiSdk\Entity\Cms\CmsItem;
use Foodpanda\ApiSdk\Exception\EntityNotFoundException;

class CmsProvider extends AbstractProvider
{
    /**
     * @var CmsApiClient
     */
    protected $client;

    /**
     * @param string $code
     *
     * @return CmsItem
     * @throws EntityNotFoundException
     */
    public function findByCode($code)
    {
        $cms = $this->serializer->denormalizeCms($this->client->getCms($code));

        $element = $cms->getItems()->filter(function (CmsItem $element) use ($code) {
            if ($element->getCode() === $code) {
                return $element;
            }
        })->first();

        if (false === $element) {
            throw new EntityNotFoundException();
        }

        return $element;
    }
}
