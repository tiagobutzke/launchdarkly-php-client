<?php

namespace Volo\EntityBundle\Entity\BogoProductBlock;

use Volo\EntityBundle\Entity\DataObject;
use Volo\EntityBundle\Entity\ProductCategoryVoucher\ProductCategoryVouchersCollection;

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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getBlockType()
    {
        return $this->block_type;
    }

    /**
     * @param string $block_type
     */
    public function setBlockType($block_type)
    {
        $this->block_type = $block_type;
    }

    /**
     * @return ProductCategoryVouchersCollection
     */
    public function getProductCategoryVoucher()
    {
        return $this->product_category_voucher;
    }

    /**
     * @param ProductCategoryVouchersCollection $product_category_voucher
     */
    public function setProductCategoryVoucher($product_category_voucher)
    {
        $this->product_category_voucher = $product_category_voucher;
    }
}
