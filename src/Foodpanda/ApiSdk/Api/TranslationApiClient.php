<?php

namespace Foodpanda\ApiSdk\Api;

class TranslationApiClient extends AbstractApiClient
{
    /**
     * @param array $arguments
     *
     * @return array
     */
    public function getTranslations(array $arguments = array())
    {
        $request = $this->client->createRequest('GET', 'translations', $arguments);

        return $this->send($request);
    }
}
