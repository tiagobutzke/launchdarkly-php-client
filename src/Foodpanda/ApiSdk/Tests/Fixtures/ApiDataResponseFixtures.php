<?php

namespace Foodpanda\ApiSdk\Tests\Fixtures;

class ApiDataResponseFixtures
{
    /**
     * @return array
     */
    public function getCmsResponseData()
    {
        $fixtureArray = $this->getResponseData('cms.json');
        $fixtureArray['available_count'] = $fixtureArray['returned_count'];

        return $fixtureArray;
    }

    /**
     * @return array
     */
    public function getVendorListResponseData()
    {
        $fixtureArray = $this->getResponseData('vendor_list.json');
        foreach ($fixtureArray['items'] as &$vendor) {
            $vendor['menus'] = [];
        }

        return $fixtureArray;
    }

    /**
     * @return array
     */
    public function getVendorResponseData()
    {
        return $this->getResponseData('vendor.json');
    }

    /**
     * @return array
     */
    public function getCalculateOrderResponseData()
    {
        return $this->getResponseData('calculate_order_response.json');
    }

    /**
     * @return array
     */
    public function getConfigurationResponseData()
    {
        return $this->getResponseData('configuration.json');
    }

    /**
     * @return array
     */
    public function getAreasResponseData()
    {
        return $this->getResponseData('areas_response.json');
    }

    /**
     * @return array
     */
    public function getCitiesResponseData()
    {
        return $this->getResponseData('cities_response.json');
    }

    /**
     * @return array
     */
    public function getCustomerReponseData()
    {
        return $this->getResponseData('customer_registration_response.json');
    }

    /**
     * @return array
     */
    public function getGuestCustomerReponseData()
    {
        return $this->getResponseData('guest_customer_response.json');
    }

    /**
     * @return array
     */
    public function getReOrderResponseData()
    {
        return $this->getResponseData('reorder_response.json');
    }

    /**
     * @param string $file
     *
     * @return array
     */
    protected function getResponseData($file)
    {
        $json = json_decode(file_get_contents(__DIR__ . '/Json/' . $file), true);

        return $json['data'];
    }
}
