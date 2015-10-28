<?php

namespace Volo\FrontendBundle\Service;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Client;
use Volo\FrontendBundle\Exception\SiteMapNotFoundException;

class SiteMapService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getSiteMap()
    {
        try {
            $response = $this->client->get('sitemap.xml');
            $xml = (string)$response->getBody();
        } catch (BadResponseException $e) {
            throw new SiteMapNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        return $xml;
    }
}
