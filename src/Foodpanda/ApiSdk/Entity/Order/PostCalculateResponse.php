<?php

namespace Foodpanda\ApiSdk\Entity\Order;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\VendorCart\VendorCartsCollection;
use Foodpanda\ApiSdk\Entity\Voucher\VouchersCollection;

class PostCalculateResponse extends DataObject
{
    /**
     * @var double
     */
    protected $subtotal;

    /**
     * @var double
     */
    protected $subtotal_before_discount;

    /**
     * @var double
     */
    protected $subtotal_after_product_discount;

    /**
     * @var double
     */
    protected $subtotal_after_discount;

    /**
     * @var double
     */
    protected $subtotal_after_discount_and_delivery_fee;

    /**
     * @var double
     */
    protected $subtotal_after_discount_and_service_fee;

    /**
     * @var double
     */
    protected $subtotal_after_discount_and_delivery_fee_and_service_fee;

    /**
     * @var double
     */
    protected $total_value;

    /**
     * @var double
     */
    protected $group_joiner_total;

    /**
     * @var double
     */
    protected $container_charge;

    /**
     * @var double
     */
    protected $delivery_fee;

    /**
     * @var double
     */
    protected $vat_total;

    /**
     * @var double
     */
    protected $voucher_total;

    /**
     * @var double
     */
    protected $discount_total;

    /**
     * @var double
     */
    protected $delivery_fee_discount;

    /**
     * @var double
     */
    protected $service_tax_total;

    /**
     * @var double
     */
    protected $service_fee_total;

    /**
     * @var VendorCartsCollection
     */
    protected $vendorCart;

    /**
     * @var VouchersCollection
     */
    protected $voucher;

    public function __construct()
    {
        $this->vendorCart = new VendorCartsCollection();
        $this->voucher = new VouchersCollection();
    }

    /**
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @return float
     */
    public function getSubtotalBeforeDiscount()
    {
        return $this->subtotal_before_discount;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterProductDiscount()
    {
        return $this->subtotal_after_product_discount;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterDiscount()
    {
        return $this->subtotal_after_discount;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterDiscountAndDeliveryFee()
    {
        return $this->subtotal_after_discount_and_delivery_fee;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterDiscountAndServiceFee()
    {
        return $this->subtotal_after_discount_and_service_fee;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterDiscountAndDeliveryFeeAndServiceFee()
    {
        return $this->subtotal_after_discount_and_delivery_fee_and_service_fee;
    }

    /**
     * @return float
     */
    public function getTotalValue()
    {
        return $this->total_value;
    }

    /**
     * @return float
     */
    public function getGroupJoinerTotal()
    {
        return $this->group_joiner_total;
    }

    /**
     * @return float
     */
    public function getContainerCharge()
    {
        return $this->container_charge;
    }

    /**
     * @return float
     */
    public function getDeliveryFee()
    {
        return $this->delivery_fee;
    }

    /**
     * @return float
     */
    public function getVatTotal()
    {
        return $this->vat_total;
    }

    /**
     * @return float
     */
    public function getVoucherTotal()
    {
        return $this->voucher_total;
    }

    /**
     * @return float
     */
    public function getDiscountTotal()
    {
        return $this->discount_total;
    }

    /**
     * @return float
     */
    public function getDeliveryFeeDiscount()
    {
        return $this->delivery_fee_discount;
    }

    /**
     * @return float
     */
    public function getServiceTaxTotal()
    {
        return $this->service_tax_total;
    }

    /**
     * @return float
     */
    public function getServiceFeeTotal()
    {
        return $this->service_fee_total;
    }

    /**
     * @return VendorCartsCollection
     */
    public function getVendorCart()
    {
        return $this->vendorCart;
    }

    /**
     * @return VouchersCollection
     */
    public function getVoucher()
    {
        return $this->voucher;
    }
}
