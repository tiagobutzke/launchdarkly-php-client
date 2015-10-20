<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Jb\Bundle\PhumborBundle\Transformer\BaseTransformer;
use Jb\Bundle\PhumborBundle\Transformer\Exception\UnknownTransformationException;
use Symfony\Component\Asset\Packages;
use Thumbor\Url\Builder;

class ThumborService
{
    /**
     * @var BaseTransformer
     */
    private $transformer;

    /**
     * @var Packages
     */
    private $packages;

    /**
     * @param BaseTransformer $transformer
     * @param Packages $packages
     */
    public function __construct(
        BaseTransformer $transformer,
        Packages $packages
    ) {
        $this->transformer = $transformer;
        $this->packages = $packages;
    }

    /**
     * @param Vendor $vendor
     * @param string $transformation
     *
     * @return Builder
     * @throws UnknownTransformationException
     */
    public function generateUrl(Vendor $vendor, $transformation)
    {
        $url = $this->packages->getUrl($vendor->getCode().'-listing.jpg', 'cdn_s3');

        $image = $this->transformer->transform($url, $transformation);

        return $image;
    }
}
