<?php

namespace Foodpanda\ApiSdk\Api;

class ConfigurationApiClient extends AbstractApiClient
{
    /**
     * @param int $languageId
     * @param string $include
     *
     * @return array
     */
    public function getConfiguration($languageId = null, $include = null)
    {
        $request = $this->client->createRequest('GET', 'configuration', [
            'language_id' => $languageId,
            'include'     => $include,
        ]);

        return $this->send($request);
    }
}
