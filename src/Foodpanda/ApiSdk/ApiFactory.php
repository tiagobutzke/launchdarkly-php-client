<?php

namespace Foodpanda\ApiSdk;

use Foodpanda\ApiSdk\Api\FoodpandaClient;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;

class ApiFactory
{
    protected static $optionsFilename;

    protected static $clientInstance;

    /**
     * @return string
     */
    public static function getOptionFilename()
    {
        if (null === static::$optionsFilename) {
            static::$optionsFilename = __DIR__ . '/config.php';
        }

        return static::$optionsFilename;
    }

    /**
     * @param string $filename
     */
    public static function setOptionFilename($filename)
    {
        static::$optionsFilename = $filename;
    }

    /**
     * @return Serializer
     */
    public static function createSerializer()
    {
        return new Serializer([new CustomNormalizer()], [new JsonEncoder()]);
    }

    /**
     * @return FoodpandaClient
     */
    public static function createApiClient()
    {
        $options = require(static::getOptionFilename());

        $config = $options['client']['config'];

        if (null === static::$clientInstance) {
            static::$clientInstance = new $options['client']['className']($config);
        }

        return static::$clientInstance;
    }
}
