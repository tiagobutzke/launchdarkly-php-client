<?php

namespace Foodpanda\ApiSdk\Entity\VendorCart;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\Product\ProductsCollection;

class VendorCart extends DataObject
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
     * @var int
     */
    protected $vendor_id;

    /**
     * @var double
     */
    protected $minimum_order_amount;

    /**
     * @var double
     */
    protected $minimum_order_amount_difference;

    /**
     * @var string
     */
    protected $discount_text;

    /**
     * @var ProductsCollection
     */
    protected $products;

    public function __construct()
    {
        $this->products = new ProductsCollection();
    }

    /**
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @param float $subtotal
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
    }

    /**
     * @return float
     */
    public function getSubtotalBeforeDiscount()
    {
        return $this->subtotal_before_discount;
    }

    /**
     * @param float $subtotal_before_discount
     */
    public function setSubtotalBeforeDiscount($subtotal_before_discount)
    {
        $this->subtotal_before_discount = $subtotal_before_discount;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterProductDiscount()
    {
        return $this->subtotal_after_product_discount;
    }

    /**
     * @param float $subtotal_after_product_discount
     */
    public function setSubtotalAfterProductDiscount($subtotal_after_product_discount)
    {
        $this->subtotal_after_product_discount = $subtotal_after_product_discount;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterDiscount()
    {
        return $this->subtotal_after_discount;
    }

    /**
     * @param float $subtotal_after_discount
     */
    public function setSubtotalAfterDiscount($subtotal_after_discount)
    {
        $this->subtotal_after_discount = $subtotal_after_discount;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterDiscountAndDeliveryFee()
    {
        return $this->subtotal_after_discount_and_delivery_fee;
    }

    /**
     * @param float $subtotal_after_discount_and_delivery_fee
     */
    public function setSubtotalAfterDiscountAndDeliveryFee($subtotal_after_discount_and_delivery_fee)
    {
        $this->subtotal_after_discount_and_delivery_fee = $subtotal_after_discount_and_delivery_fee;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterDiscountAndServiceFee()
    {
        return $this->subtotal_after_discount_and_service_fee;
    }

    /**
     * @param float $subtotal_after_discount_and_service_fee
     */
    public function setSubtotalAfterDiscountAndServiceFee($subtotal_after_discount_and_service_fee)
    {
        $this->subtotal_after_discount_and_service_fee = $subtotal_after_discount_and_service_fee;
    }

    /**
     * @return float
     */
    public function getSubtotalAfterDiscountAndDeliveryFeeAndServiceFee()
    {
        return $this->subtotal_after_discount_and_delivery_fee_and_service_fee;
    }

    /**
     * @param float $subtotal_after_discount_and_delivery_fee_and_service_fee
     */
    public function setSubtotalAfterDiscountAndDeliveryFeeAndServiceFee(
        $subtotal_after_discount_and_delivery_fee_and_service_fee
    ) {
        $this->subtotal_after_discount_and_delivery_fee_and_service_fee
            = $subtotal_after_discount_and_delivery_fee_and_service_fee;
    }

    /**
     * @return float
     */
    public function getTotalValue()
    {
        return $this->total_value;
    }

    /**
     * @param float $total_value
     */
    public function setTotalValue($total_value)
    {
        $this->total_value = $total_value;
    }

    /**
     * @return float
     */
    public function getGroupJoinerTotal()
    {
        return $this->group_joiner_total;
    }

    /**
     * @param float $group_joiner_total
     */
    public function setGroupJoinerTotal($group_joiner_total)
    {
        $this->group_joiner_total = $group_joiner_total;
    }

    /**
     * @return float
     */
    public function getContainerCharge()
    {
        return $this->container_charge;
    }

    /**
     * @param float $container_charge
     */
    public function setContainerCharge($container_charge)
    {
        $this->container_charge = $container_charge;
    }

    /**
     * @return float
     */
    public function getDeliveryFee()
    {
        return $this->delivery_fee;
    }

    /**
     * @param float $delivery_fee
     */
    public function setDeliveryFee($delivery_fee)
    {
        $this->delivery_fee = $delivery_fee;
    }

    /**
     * @return float
     */
    public function getVatTotal()
    {
        return $this->vat_total;
    }

    /**
     * @param float $vat_total
     */
    public function setVatTotal($vat_total)
    {
        $this->vat_total = $vat_total;
    }

    /**
     * @return float
     */
    public function getVoucherTotal()
    {
        return $this->voucher_total;
    }

    /**
     * @param float $voucher_total
     */
    public function setVoucherTotal($voucher_total)
    {
        $this->voucher_total = $voucher_total;
    }

    /**
     * @return float
     */
    public function getDiscountTotal()
    {
        return $this->discount_total;
    }

    /**
     * @param float $discount_total
     */
    public function setDiscountTotal($discount_total)
    {
        $this->discount_total = $discount_total;
    }

    /**
     * @return float
     */
    public function getDeliveryFeeDiscount()
    {
        return $this->delivery_fee_discount;
    }

    /**
     * @param float $delivery_fee_discount
     */
    public function setDeliveryFeeDiscount($delivery_fee_discount)
    {
        $this->delivery_fee_discount = $delivery_fee_discount;
    }

    /**
     * @return float
     */
    public function getServiceTaxTotal()
    {
        return $this->service_tax_total;
    }

    /**
     * @param float $service_tax_total
     */
    public function setServiceTaxTotal($service_tax_total)
    {
        $this->service_tax_total = $service_tax_total;
    }

    /**
     * @return float
     */
    public function getServiceFeeTotal()
    {
        return $this->service_fee_total;
    }

    /**
     * @param float $service_fee_total
     */
    public function setServiceFeeTotal($service_fee_total)
    {
        $this->service_fee_total = $service_fee_total;
    }

    /**
     * @return int
     */
    public function getVendorId()
    {
        return $this->vendor_id;
    }

    /**
     * @param int $vendor_id
     */
    public function setVendorId($vendor_id)
    {
        $this->vendor_id = $vendor_id;
    }

    /**
     * @return float
     */
    public function getMinimumOrderAmount()
    {
        return $this->minimum_order_amount;
    }

    /**
     * @param float $minimum_order_amount
     */
    public function setMinimumOrderAmount($minimum_order_amount)
    {
        $this->minimum_order_amount = $minimum_order_amount;
    }

    /**
     * @return float
     */
    public function getMinimumOrderAmountDifference()
    {
        return $this->minimum_order_amount_difference;
    }

    /**
     * @param float $minimum_order_amount_difference
     */
    public function setMinimumOrderAmountDifference($minimum_order_amount_difference)
    {
        $this->minimum_order_amount_difference = $minimum_order_amount_difference;
    }

    /**
     * @return string
     */
    public function getDiscountText()
    {
        return $this->discount_text;
    }

    /**
     * @param string $discount_text
     */
    public function setDiscountText($discount_text)
    {
        $this->discount_text = $discount_text;
    }

    /**
     * @return ProductsCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ProductsCollection $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }
}
