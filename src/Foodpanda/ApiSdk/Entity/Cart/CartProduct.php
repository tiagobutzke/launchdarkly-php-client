<?php

namespace Foodpanda\ApiSdk\Entity\Cart;

use Foodpanda\ApiSdk\Entity\VendorCart\VendorCartProduct;

class CartProduct extends VendorCartProduct
{
    /**
     * @var int
     */
    protected $vendor_id;

    /**
     * @var int
     */
    protected $variation_id;

    /**
     * @return int
     */
    public function getVariationId()
    {
        return $this->variation_id;
    }

    /**
     * @param int $vendorId
     */
    public function setVendorId($vendorId)
    {
        $this->vendor_id = $vendorId;
    }

    /**
     * @return int
     */
    public function getVendorId()
    {
        return $this->vendor_id;
    }

    /**
     * @param int $variationId
     */
    public function setVariationId($variationId)
    {
        $this->variation_id = $variationId;
    }
}
