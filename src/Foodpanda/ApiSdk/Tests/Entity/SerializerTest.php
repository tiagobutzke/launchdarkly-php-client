<?php

namespace Foodpanda\ApiSdk\Tests\Entity;

use Foodpanda\ApiSdk\ApiFactory;
use Foodpanda\ApiSdk\Entity\Address\Address;
use Foodpanda\ApiSdk\Entity\Address\AddressesCollection;
use Foodpanda\ApiSdk\Entity\Choice\ChoicesCollection;
use Foodpanda\ApiSdk\Entity\City\CitiesCollection;
use Foodpanda\ApiSdk\Entity\City\CityResults;
use Foodpanda\ApiSdk\Entity\Cms\CmsItemCollection;
use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Foodpanda\ApiSdk\Entity\Geocoding\Area;
use Foodpanda\ApiSdk\Entity\Geocoding\AreaResults;
use Foodpanda\ApiSdk\Entity\Geocoding\AreasCollection;
use Foodpanda\ApiSdk\Entity\Order\GuestCustomer;
use Foodpanda\ApiSdk\Entity\Order\PostCalculateResponse;
use Foodpanda\ApiSdk\Entity\Reorder\Reorder;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderChoice;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderChoicesCollection;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderProduct;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderProductsCollection;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderProductVariation;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderResults;
use Foodpanda\ApiSdk\Entity\Reorder\ReordersCollection;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderTopping;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderToppingsCollection;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderVendor;
use Foodpanda\ApiSdk\Entity\Reorder\ReorderVendorsCollection;
use Foodpanda\ApiSdk\Entity\Topping\Topping;
use Foodpanda\ApiSdk\Entity\Topping\ToppingsCollection;
use Foodpanda\ApiSdk\Entity\Vendor\VendorsCollection;
use Foodpanda\ApiSdk\Entity\VendorCart\VendorCart;
use Foodpanda\ApiSdk\Entity\VendorCart\VendorCartProduct;
use Foodpanda\ApiSdk\Entity\VendorCart\VendorCartProductsCollection;
use Foodpanda\ApiSdk\Entity\VendorCart\VendorCartsCollection;
use Foodpanda\ApiSdk\Entity\Voucher\VouchersCollection;
use Foodpanda\ApiSdk\Serializer;
use Foodpanda\ApiSdk\Tests\Fixtures\ApiDataResponseFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Foodpanda\ApiSdk\Entity\Chain\Chain;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\Cms\CmsResults;
use Foodpanda\ApiSdk\Entity\Configuration\PaymentFormConfiguration;
use Foodpanda\ApiSdk\Entity\Configuration\SocialConnects;
use Foodpanda\ApiSdk\Entity\Cuisine\Cuisine;
use Foodpanda\ApiSdk\Entity\Cuisine\CuisinesCollection;
use Foodpanda\ApiSdk\Entity\Customer\CustomerAddressConfiguration;
use Foodpanda\ApiSdk\Entity\Customer\CustomerConfiguration;
use Foodpanda\ApiSdk\Entity\Event\EventsCollection;
use Foodpanda\ApiSdk\Entity\FoodCharacteristics\FoodCharacteristics;
use Foodpanda\ApiSdk\Entity\FoodCharacteristics\FoodCharacteristicsCollection;
use Foodpanda\ApiSdk\Entity\FormElement\FormElement;
use Foodpanda\ApiSdk\Entity\FormElement\FormElementsCollection;
use Foodpanda\ApiSdk\Entity\Language\Language;
use Foodpanda\ApiSdk\Entity\Language\LanguagesCollection;
use Foodpanda\ApiSdk\Entity\Menu\Menu;
use Foodpanda\ApiSdk\Entity\Menu\MenusCollection;
use Foodpanda\ApiSdk\Entity\MenuCategory\MenuCategoriesCollection;
use Foodpanda\ApiSdk\Entity\MenuCategory\MenuCategory;
use Foodpanda\ApiSdk\Entity\Product\Product;
use Foodpanda\ApiSdk\Entity\Product\ProductsCollection;
use Foodpanda\ApiSdk\Entity\ProductVariation\ProductVariation;
use Foodpanda\ApiSdk\Entity\ProductVariation\ProductVariationsCollection;
use Foodpanda\ApiSdk\Entity\Schedule\Schedule;
use Foodpanda\ApiSdk\Entity\Schedule\SchedulesCollection;
use Foodpanda\ApiSdk\Entity\Vendor\MetaData;
use Foodpanda\ApiSdk\Entity\Vendor\Vendor;
use Foodpanda\ApiSdk\Entity\Vendor\VendorResults;

class SerializerTest extends WebTestCase
{
    /**
     * @var ApiDataResponseFixtures
     */
    protected $apiDataResponseFixtures;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function setUp()
    {
        $this->serializer = ApiFactory::createSerializer();
        $this->apiDataResponseFixtures = new ApiDataResponseFixtures();
    }

    public function testDenormalizeCms()
    {
        $sourceData = $this->apiDataResponseFixtures->getCmsResponseData();
        $entity = $this->serializer->denormalizeCms($sourceData);
        $this->assertInstanceOf(CmsResults::class, $entity);
        $this->assertInstanceOf(CmsItemCollection::class, $entity->getItems());

        $resultData = $this->serializer->normalize($entity);
        $this->assertEquals($sourceData, $resultData);
    }

    public function testDenormalizeVendorList()
    {
        $sourceData = $this->apiDataResponseFixtures->getVendorListResponseData();
        $entity = $this->serializer->denormalizeVendors($sourceData);
        $this->assertInstanceOf(VendorsCollection::class, $entity->getItems());
        $this->assertInstanceOf(VendorResults::class, $entity);

        $resultsData = $this->serializer->normalize($entity);
        $this->assertEquals($sourceData, $resultsData);
    }

    public function testDenormalizeVendor()
    {
        $sourceData = $this->apiDataResponseFixtures->getVendorResponseData();
        $entity = $this->serializer->denormalizeVendor($sourceData);
        $resultsData = $this->serializer->normalize($entity);
        $this->assertTheValidityOfVendorEntityStructure($entity);

        $this->assertEquals($sourceData, $resultsData);
    }

    public function testDenormalizeConfiguration()
    {
        $sourceData = $this->apiDataResponseFixtures->getConfigurationResponseData();
        $entity = $this->serializer->denormalizeConfiguration($sourceData);

        $this->assertInstanceOf(SocialConnects::class, $entity->getEnabledSocialConnects());

        $this->assertInstanceOf(
            FoodCharacteristicsCollection::class,
            $entity->getFoodCharacteristicAvailableFilters()
        );
        $this->assertInstanceOf(FoodCharacteristics::class, $entity->getFoodCharacteristicAvailableFilters()->first());

        $this->assertInstanceOf(LanguagesCollection::class, $entity->getLanguages());
        $this->assertInstanceOf(Language::class, $entity->getLanguages()->first());

        // Customer Config
        $this->assertInstanceOf(CustomerConfiguration::class, $entity->getCustomerConfiguration());
        $this->assertInstanceOf(FormElementsCollection::class, $entity->getCustomerConfiguration()->getFormElements());
        $this->assertInstanceOf(FormElement::class, $entity->getCustomerConfiguration()->getFormElements()->first());

        // Customer Address Config
        $this->assertInstanceOf(CustomerAddressConfiguration::class, $entity->getCustomerAddressConfiguration());
        $this->assertInstanceOf(
            FormElementsCollection::class,
            $entity->getCustomerAddressConfiguration()->getFormElements()
        );
        $this->assertInstanceOf(
            FormElement::class,
            $entity->getCustomerAddressConfiguration()->getFormElements()->first()
        );

        // Payment Form Config
        $this->assertInstanceOf(PaymentFormConfiguration::class, $entity->getPaymentFormConfiguration());
        $this->assertInstanceOf(
            FormElementsCollection::class,
            $entity->getPaymentFormConfiguration()->getFormElements()
        );
        $this->assertInstanceOf(
            FormElement::class,
            $entity->getPaymentFormConfiguration()->getFormElements()->first()
        );

        $resultData = $this->serializer->normalize($entity);

        $this->assertEquals($sourceData, $resultData);
    }

    public function testDenormalizeCalculateOrder()
    {
        $sourceData = $this->apiDataResponseFixtures->getCalculateOrderResponseData();
        $entity = $this->serializer->denormalizePostCalculateReponse($sourceData);

        $this->assertInstanceOf(PostCalculateResponse::class, $entity);
        $this->assertInstanceOf(VouchersCollection::class, $entity->getVoucher());

        $this->assertInstanceOf(VendorCartsCollection::class, $entity->getVendorCart());
        /** @var VendorCart $vendorCart */
        $vendorCart = $entity->getVendorCart()->first();
        $this->assertInstanceOf(VendorCart::class, $vendorCart);
        $this->assertInstanceOf(VendorCartProductsCollection::class, $vendorCart->getProducts());

        /** @var VendorCartProduct $product */
        $product = $vendorCart->getProducts()->first();
        $this->assertInstanceOf(VendorCartProduct::class, $product);
        $this->assertInstanceOf(ChoicesCollection::class, $product->getChoices());
        $this->assertInstanceOf(ToppingsCollection::class, $product->getToppings());
        $this->assertInstanceOf(Topping::class, $product->getToppings()->first());

        $resultData = $this->serializer->normalize($entity);

        $this->assertEquals($sourceData, $resultData);
    }

    public function testDenormalizeAreas()
    {
        $sourceData = $this->apiDataResponseFixtures->getAreasResponseData();
        $entity = $this->serializer->denormalizeGeocodingAreas($sourceData);

        $this->assertInstanceOf(AreaResults::class, $entity);
        $this->assertInstanceOf(AreasCollection::class, $entity->getItems());
        $this->assertInstanceOf(Area::class, $entity->getItems()->first());

        $resultData = $this->serializer->normalize($entity);

        $this->assertEquals($sourceData, $resultData);
    }

    public function testDenormalizeCustomer()
    {
        $sourceData = $this->apiDataResponseFixtures->getCustomerReponseData();
        $entity = $this->serializer->denormalizeCustomer($sourceData);
        $this->assertInstanceOf(Customer::class, $entity);
        $this->assertInstanceOf(AddressesCollection::class, $entity->getCustomerAddresses());

        $resultData = $this->serializer->normalize($entity);

        $this->assertEquals($sourceData, $resultData);
    }

    public function testDenormalizeGuestCustomer()
    {
        $sourceData = $this->apiDataResponseFixtures->getGuestCustomerReponseData();
        $entity = $this->serializer->denormalizeGuestCustomer($sourceData);
        $this->assertInstanceOf(GuestCustomer::class, $entity);
        $this->assertInstanceOf(Customer::class, $entity->getCustomer());
        $this->assertInstanceOf(Address::class, $entity->getCustomerAddress());

        $resultData = $this->serializer->normalize($entity);

        $this->assertEquals($sourceData, $resultData);
    }

    public function testDenormalizeCities()
    {
        $sourceData = $this->apiDataResponseFixtures->getCitiesResponseData();
        $entity = $this->serializer->denormalizeCities($sourceData);

        $this->assertInstanceOf(CityResults::class, $entity);
        $this->assertInstanceOf(CitiesCollection::class, $entity->getItems());
        $this->assertInstanceOf(City::class, $entity->getItems()->first());

        $resultData = $this->serializer->normalize($entity);

        $this->assertEquals($sourceData, $resultData);
    }

    public function testDenormalizeReorders()
    {
        $sourceData = $this->apiDataResponseFixtures->getReOrderResponseData();
        $entity = $this->serializer->denormalizePreordersResponse($sourceData);
        
        $this->assertInstanceOf(ReorderResults::class, $entity);
        $this->assertInstanceOf(ReordersCollection::class, $entity->getItems());
        /** @var Reorder $reorder */
        $reorder = $entity->getItems()->first();
        $this->assertInstanceOf(Reorder::class, $reorder);

        $this->assertInstanceOf(ReorderVendorsCollection::class, $reorder->getVendors());

        /** @var ReorderVendor $vendor */
        $vendor = $reorder->getVendors()->first();
        $this->assertInstanceOf(ReorderVendor::class, $vendor);
        $this->assertInstanceOf(ReorderProductsCollection::class, $vendor->getProducts());

        /** @var ReorderProduct $product */
        $product = $vendor->getProducts()->first();
        $this->assertInstanceOf(ReorderProduct::class, $product);
        $this->assertInstanceOf(ReorderProductVariation::class, $product->getProductVariation());
        $this->assertInstanceOf(ReorderChoicesCollection::class, $product->getProductVariation()->getChoices());
        $this->assertInstanceOf(ReorderChoice::class, $product->getProductVariation()->getChoices()->first());

        $this->assertInstanceOf(ReorderToppingsCollection::class, $product->getProductVariation()->getToppings());
        $this->assertInstanceOf(ReorderTopping::class, $product->getProductVariation()->getToppings()->first());
    }

    /**
     * @param Vendor $entity
     */
    protected function assertTheValidityOfVendorEntityStructure(Vendor $entity)
    {
        $this->assertInstanceOf(Vendor::class, $entity);
        $this->assertInstanceOf(MenusCollection::class, $entity->getMenus());

        /** @var Menu $menu */
        $menu = $entity->getMenus()->first();
        $this->assertInstanceOf(Menu::class, $menu);
        $this->assertInstanceOf(MenuCategoriesCollection::class, $menu->getMenuCategories());

        /** @var MenuCategory $menuCategory */
        $menuCategory = $menu->getMenuCategories()->first();
        $this->assertInstanceOf(MenuCategory::class, $menuCategory);

        $this->assertInstanceOf(ProductsCollection::class, $menuCategory->getProducts());

        /** @var Product $product */
        $product = $menuCategory->getProducts()->first();
        $this->assertInstanceOf(Product::class, $product);
        $this->assertInstanceOf(ProductVariationsCollection::class, $product->getProductVariations());

        /** @var ProductVariation $productVariation */
        $productVariation = $product->getProductVariations()->first();
        $this->assertInstanceOf(ProductVariation::class, $productVariation);

        $this->assertInstanceOf(Chain::class, $entity->getChain());
        $this->assertInstanceOf(City::class, $entity->getCity());

        $this->assertInstanceOf(CuisinesCollection::class, $entity->getCuisines());
        $this->assertInstanceOf(Cuisine::class, $entity->getCuisines()->first());

        $this->assertInstanceOf(MetaData::class, $entity->getMetadata());
        $this->assertInstanceOf(EventsCollection::class, $entity->getMetadata()->getEvents());

        $this->assertInstanceOf(SchedulesCollection::class, $entity->getSchedules());
        $this->assertInstanceOf(Schedule::class, $entity->getSchedules()->first());


        $this->assertInstanceOf(FoodCharacteristicsCollection::class, $entity->getFoodCharacteristics());
    }
}
