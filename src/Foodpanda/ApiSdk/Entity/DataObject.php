<?php

namespace Foodpanda\ApiSdk\Entity;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class DataObject implements DenormalizableInterface, NormalizableInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = array())
    {
        foreach ($data as $key => $value) {
            if (null === $value) {
                continue;
            }

            if (is_object($this->$key) && $denormalizer->supportsDenormalization($this->$key, get_class($this->$key))) {
                $value = $denormalizer->denormalize($value, get_class($this->$key), $format, $context);
            }

            $this->$key = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        $keys = array_keys(get_class_vars(get_class($this)));
        $keyValuePairs = [];
        foreach ($keys as $key) {
            if (is_object($this->$key) && $normalizer->supportsNormalization($this->$key)) {
                $keyValuePairs[$key] = $normalizer->normalize($this->$key, $format, $context);
            } else {
                $keyValuePairs[$key] = $this->$key;
            }
        }

        return $keyValuePairs;
    }
}
