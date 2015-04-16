<?php

namespace Volo\ApiClientBundle\Api;

class CustomerApiClient extends AbstractApiClient
{
    /**
     * @param array $arguments
     *
     * @return array
     */
    public function getCustomers(array $arguments = array())
    {
        $request = $this->client->createRequest('GET', 'customers', $arguments);

        return $this->send($request);
    }
}
