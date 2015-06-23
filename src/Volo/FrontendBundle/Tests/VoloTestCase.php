<?php

namespace Volo\FrontendBundle\Tests;

use Foodpanda\ApiSdk\ApiFactory;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

abstract class VoloTestCase extends WebTestCase
{
    /**
     * @var Client
     */
    protected $client;

    protected function setUp()
    {
        parent::setUp();

        ApiFactory::setOptionFilename(__DIR__ . '/../../../Foodpanda/ApiSdk/Tests/config_test.php');
    }

    protected function assertValidResponse(Client $client)
    {
        $response = $client->getResponse();
        $message = sprintf(
            'Url "%s" failed with error "%d".',
            $client->getRequest()->getRequestUri(),
            $response->getStatusCode()
        );

        dump($response->getContent());
        static::assertTrue($response->isSuccessful(), $message);
    }


    /**
     * Checks the success state of a response
     *
     * @param Response $response Response object
     * @param bool $success to define whether the response is expected to be successful
     * @param string $type
     *
     * @return void
     */
    public function isSuccessful($response, $success = true, $type = 'text/html')
    {
        try {
            $crawler = new Crawler();
            $crawler->addContent($response->getContent(), $type);
            if (! count($crawler->filter('title'))) {
                $title = '['.$response->getStatusCode().'] - '.$response->getContent();
            } else {
                $title = $crawler->filter('title')->text();
            }
        } catch (\Exception $e) {
            $title = $e->getMessage();
        }

        $message = $this->formatErrorMessage($response, $title);
        if ($success) {
            $this->assertTrue($response->isSuccessful(), 'The Response was not successful: ' . $message);
        } else {

            $this->assertFalse($response->isSuccessful(), 'The Response was not successful: ' . $message);
        }
    }

    /**
     * @param $response
     * @param $title
     * @return string
     */
    protected function formatErrorMessage(Response $response, $title)
    {
        $message = $title;
        if ($response->headers->get('Content-Type') === 'application/json') {
            $message = json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
        }

        return $message;
    }
}
