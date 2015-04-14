<?php

namespace Volo\EntityBundle\Service;

use Symfony\Component\Serializer\Serializer;
use Volo\EntityBundle\Entity\Cms\CmsResults;

class EntityGenerator
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
     * @param array $data
     *
     * @return Data
     */
    public function generateCms(array $data)
    {
        return $this->serializer->denormalize($data, CmsResults::class);
    }
}
