<?php

namespace Foodpanda\ApiSdk;

use Symfony\Component\Serializer\Normalizer\CustomNormalizer;

class ApiFactory
{
    /**
     * @return Serializer
     */
    public static function createSerializer()
    {
        return new Serializer([new CustomNormalizer()]);
    }
}
