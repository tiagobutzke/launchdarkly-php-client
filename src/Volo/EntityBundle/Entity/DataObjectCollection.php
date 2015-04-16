<?php

namespace Volo\EntityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class DataObjectCollection extends ArrayCollection implements DenormalizableInterface, NormalizableInterface
{
    /**
     * {@inheritdoc}
     */
    abstract protected function getCollectionItemClass();

    /**
     * {@inheritdoc}
     */
    public function denormalize(DenormalizerInterface $denormalizer, $items, $format = null, array $context = array())
    {
        $className = $this->getCollectionItemClass();
        if (is_array($items)) {
            foreach ($items as $itemValues) {
                $item = $denormalizer->denormalize($itemValues, $className, $format, $context);
                $this->add($item);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array())
    {
        $keyValuePairs = [];

        foreach ($this->toArray() as $item) {
            if (is_object($item) && $normalizer->supportsNormalization($item)) {
                $keyValuePairs[] = $normalizer->normalize($item, $format, $context);
            } else {
                $keyValuePairs[] = $item;
            }
        }

        return $keyValuePairs;
    }
}
