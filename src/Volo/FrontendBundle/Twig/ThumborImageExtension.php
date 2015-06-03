<?php

namespace Volo\FrontendBundle\Twig;

use Thumbor\Url;

class ThumborImageExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    private $transformers;

    /**
     * @param array $transformers
     */
    public function __construct(array $transformers = [])
    {
        $this->transformers = $transformers;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_thumbor_configuration', array($this, 'getThumborConfiguration'))
        );
    }

    /**
     * @return array
     */
    public function getThumborConfiguration()
    {
        $output = [];
        foreach ($this->transformers as $key => $transformer) {
            if (false !== strpos($key, 'bp_')) {
                if (false === strrpos($key, '_retina')) {
                    $output[$key]['normal'] = [
                        'key' => $key,
                        'transformer' => $transformer,
                    ];
                } else {
                    $output[substr($key, 0, strrpos($key, '_retina'))]['retina'] = [
                        'key' => $key,
                        'transformer' => $transformer,
                    ];
                }
            }
        }

        return $output;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'volo_frontend.thumbor_image_extension';
    }
}
