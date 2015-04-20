<?php

namespace Volo\EntityBundle\Entity;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class DataObject implements DenormalizableInterface, NormalizableInterface
{
    /**
     * @var array
     */
    protected $objectClasses = [];

    /**
     * Defines the list or properties that should be excluded in normalization
     * @var array
     */
    protected $excludeNormalization = [
        'objectClasses',
        'excludeNormalization',
    ];

    /**
     * {@inheritdoc}
     */
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = array())
    {
        foreach ($data as $key => $value) {
            if (is_object($this->$key) && $denormalizer->supportsDenormalization($this->$key, get_class($this->$key))) {
                $value = $denormalizer->denormalize($value, get_class($this->$key), $format, $context);
            } elseif (array_key_exists($key, $this->objectClasses)) {
                $value = $denormalizer->denormalize($value, $this->objectClasses[$key], $format, $context);
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
            if ($this->isNormalizableProperty($key)) {
                if (is_object($this->$key) && $normalizer->supportsNormalization($this->$key)) {
                    $keyValuePairs[$key] = $normalizer->normalize($this->$key, $format, $context);
                } else {
                    $keyValuePairs[$key] = $this->$key;
                }
            }
        }

        return $keyValuePairs;
    }

    /**
     * @param $propertyName
     *
     * @return bool
     */
    protected function isNormalizableProperty($propertyName)
    {
        return !in_array($propertyName, $this->excludeNormalization, true);
    }
}
