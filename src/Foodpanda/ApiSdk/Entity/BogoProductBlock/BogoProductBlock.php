<?php

namespace Foodpanda\ApiSdk\Entity\BogoProductBlock;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\ProductCategoryVoucher\ProductCategoryVouchersCollection;

class BogoProductBlock extends DataObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $block_type;

    /**
     * @var ProductCategoryVouchersCollection
     */
    protected $product_category_voucher;

    public function __construct()
    {
        $this->product_category_voucher = new ProductCategoryVouchersCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return $this->block_type;
    }

    /**
     * @return ProductCategoryVouchersCollection
     */
    public function getProductCategoryVoucher()
    {
        return $this->product_category_voucher;
    }
}
