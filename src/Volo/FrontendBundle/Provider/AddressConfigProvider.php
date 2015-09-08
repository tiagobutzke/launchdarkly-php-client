<?php

namespace Volo\FrontendBundle\Provider;

class AddressConfigProvider
{
    const AUTOCOMPLETE_TYPE_REGIONS = '(regions)';

    /**
     * @var array
     */
    protected $locationConfig;

    /**
     * @param array $locationConfig
     */
    public function __construct(array $locationConfig)
    {
        $this->locationConfig = $locationConfig;
    }

    /**
     * @return bool
     */
    public function isFullAddressAutocomplete()
    {
        return count($this->locationConfig['autocomplete_type']) === 0 ||
            $this->locationConfig['autocomplete_type'][0] !== static::AUTOCOMPLETE_TYPE_REGIONS;
    }
}
