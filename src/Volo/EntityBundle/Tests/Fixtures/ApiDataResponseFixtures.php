<?php

namespace Volo\EntityBundle\Tests\Fixtures;

use Symfony\Component\Config\Definition\Exception\Exception;

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

    public function getVendorResponseData()
    {
        return $this->getResponseData('vendor.json');
    }

    /**
     * @return array
     */
    public function getConfigurationResponseData()
    {
        return $this->getResponseData('configuration.json');
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
