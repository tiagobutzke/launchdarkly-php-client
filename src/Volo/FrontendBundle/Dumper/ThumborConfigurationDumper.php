<?php

namespace Volo\FrontendBundle\Dumper;

class ThumborConfigurationDumper
{
    /**
     * @var array
     */
    protected $transformations;

    /**
     * @param array $transformations
     */
    public function __construct(array $transformations)
    {
        $this->transformations = $transformations;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $content = [
            'breakpoints' => [],
        ];

        foreach ($this->transformations as $key => $transformer) {
            if (false === strrpos($key, '_retina')) {
                $content['breakpoints'][] = [
                    'width' => $transformer['resize']['width'],
                    'src' => 'data-src-' . $key,
                    'mode' => 'viewport',
                ];
            }
        }
        return sprintf('var volo_thumbor_transformations = %s;', json_encode($content));
    }
}
