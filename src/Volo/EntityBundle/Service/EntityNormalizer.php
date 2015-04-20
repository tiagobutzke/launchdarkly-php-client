<?php

namespace Volo\EntityBundle\Service;

use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Serializer;

class EntityNormalizer
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param NormalizableInterface $entity
     *
     * @return array
     */
    public function normalizeEntity(NormalizableInterface $entity)
    {
        return $this->serializer->normalize($entity);
    }
}
