<?php

namespace Volo\FrontendBundle\Twig;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Serializer;

class SerializerExtension extends \Twig_Extension
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
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('serialize', array($this, 'serialize')),
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'serializer_extension';
    }

    /**
     * @param DataObject $data
     *
     * @return string The json representation of the entity
     */
    public function serialize(DataObject $data)
    {
        return $this->serializer->serialize($data, 'json');
    }
}
