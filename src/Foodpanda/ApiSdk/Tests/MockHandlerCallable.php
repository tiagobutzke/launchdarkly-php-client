<?php

namespace Foodpanda\ApiSdk\Tests;

use GuzzleHttp\Message\Response;
use GuzzleHttp\Post\PostBody;

class MockHandlerCallable
{
    /**
     * @param array $request
     *
     * @return Response
     * @throws \Exception
     */
    public function __invoke(array $request)
    {
        /** @var PostBody $body */
        $body = $request['body'];

        if (strpos($request['url'], 'oauth2/token') !== false && $body->getField('password') === 'good') {
            return ['status' => 200, 'body' => $this->loadDataFromFile('post-oauth2-token_good_credentials.json')];
        }
        if (strpos($request['url'], 'oauth2/token') !== false && $body->getField('password') === 'bad') {
            return ['status' => 400, 'body' => $this->loadDataFromFile('post-oauth2-token_bad_credentials.json')];
        }
        if (strpos($request['url'], 'customers') !== false && $request['http_method'] === 'GET') {
            return ['status' => 200, 'body' => $this->loadDataFromFile('get-customers.json')];
        }
        if (strpos($request['uri'], '/cities') !== false && $request['http_method'] === 'GET') {
            return ['status' => 200, 'body' => $this->loadDataFromFile('get-cities.json')];
        }

        throw new \RuntimeException(sprintf('Missing fixture for "%s" API call', $request['uri']));
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    protected function loadDataFromFile($fileName)
    {
        return file_get_contents(sprintf('%s/../Fixtures/%s', __DIR__, $fileName));
    }
}
