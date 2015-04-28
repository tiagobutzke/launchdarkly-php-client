<?php

namespace Foodpanda\ApiSdk\Provider;

use Foodpanda\ApiSdk\Entity\Cms\CmsItem;
use Foodpanda\ApiSdk\Entity\Cms\CmsResults;
use Foodpanda\ApiSdk\Exception\EntityNotFoundException;
use GuzzleHttp\Message\RequestInterface;

class CmsProvider extends AbstractProvider
{
    /**
     * @param string $code
     *
     * @return CmsItem
     * @throws EntityNotFoundException
     */
    public function findByCode($code)
    {
        $data = $this->client->send($this->getCmsResponse())['data'];

        $cms = $this->serializer->denormalizeCms($data);

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

    /**
     * @return CmsResults
     */
    public function getCms()
    {
        $data = $this->client->send($this->getCmsResponse())['data'];

        return $this->serializer->denormalizeCms($data);
    }

    /**
     * @return RequestInterface
     */
    protected function getCmsResponse()
    {
        $request = $this->client->createRequest(
            'GET',
            [
                'cms?mobilePagesOnly={mobilePagesOnly}',
                ['mobilePagesOnly' => false]
            ]
        );

        return $request;
    }
}
