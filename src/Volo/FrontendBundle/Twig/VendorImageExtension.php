<?php

namespace Volo\FrontendBundle\Twig;

use Thumbor\Url\BuilderFactory;
use Thumbor\Url;

class VendorImageExtension extends \Twig_Extension
{

    /**
     * @var string Base URL of image server (normally an S3 bucket)
     */
    private $imageServerBaseUrl;

    /**
     * @var BuilderFactory Factory which caches thumbor server URL & secret
     */
    private $builderFactory;

    /**
     * @var string $countryCode
     */
    private $countryCode;

    /**
     * @var string $environment
     */
    private $environment;

    /**
     * @param string $imageServerBaseUrl
     * @param string $countryCode
     * @param string $environment
     * @param BuilderFactory $thumborBuilderFactory
     */
    public function __construct($imageServerBaseUrl, $countryCode, $environment, BuilderFactory $thumborBuilderFactory)
    {
        $this->imageServerBaseUrl = $imageServerBaseUrl;
        $this->countryCode = $countryCode;
        $this->environment = $environment;
        $this->builderFactory = $thumborBuilderFactory;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('vendor_listing_image_url', array($this, 'getListingImageUrl')),
            new \Twig_SimpleFunction('vendor_hero_image_url', array($this, 'getHeroImageUrl'))
        );
    }

    /**
     * @param $vendorCode
     * @param $options
     *
     * @return Thumbor\Url
     */
    public function getListingImageUrl($vendorCode)
    {
        $commands = func_get_args();
        array_shift($commands);
        return $this->getImageUrl($vendorCode, 'listing', $commands);
    }

    /**
     * @param $vendorCode
     * @param $options
     *
     * @return Thumbor\Url
     */
    public function getHeroImageUrl($vendorCode)
    {
        $commands = func_get_args();
        array_shift($commands);
        return $this->getImageUrl($vendorCode, 'hero', $commands);
    }

    /**
     * @param string $vendorCode
     * @param string $imageType
     * @param array $commands
     *
     * @return Thumbor\Url
     */
    private function getImageUrl($vendorCode, $imageType, array $command_args)
    {
        $original = sprintf("%s/%s/%s/%s-%s.jpg", $this->imageServerBaseUrl, $this->environment, $this->countryCode, $vendorCode, $imageType);
        $builder = $this->builderFactory->url($original);
        foreach ($command_args as $command_arg) {
            $command_name = array_shift($command_arg);
            call_user_func_array(array($builder, $command_name), $command_arg);
        }
        return $builder->build();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vendor_image_extension';
    }

}
