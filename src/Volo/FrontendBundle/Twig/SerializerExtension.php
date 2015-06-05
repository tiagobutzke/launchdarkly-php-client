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
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array_merge(parent::getFilters(), [
            new \Twig_SimpleFilter('to_escaped_json', array($this, 'toEscapedJson'), ['is_safe' => ['html']]),
        ]);
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('serialize', array($this, 'serialize'), ['is_safe' => ['html']]),
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
     * @param mixed $data
     *
     * @return string
     */
    public function toEscapedJson($data)
    {
        return json_encode($data, $this->getJsonOptions());
    }

    /**
     * @return int
     */
    protected function getJsonOptions()
    {
        return JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
    }

    /**
     * @param DataObject $data
     *
     * @return string The json representation of the entity
     */
    public function serialize(DataObject $data)
    {
        return $this->serializer->serialize($data, 'json', ['json_encode_options' => $this->getJsonOptions()]);
    }
}
