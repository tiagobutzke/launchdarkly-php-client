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
            if (false !== strpos($key, 'bp_')) {
                if (false === strrpos($key, '_retina')) {
                    if(false === strrpos($key, 'default')) {
                        $content['breakpoints'][] = [
                            'width' => $transformer['resize']['width'],
                            'src' => 'data-src-' . $key,
                            'mode' => 'viewport',
                        ];
                    }
                }
            }
        }
        return sprintf('var volo_thumbor_transformations = %s;', json_encode($content));
    }
}
