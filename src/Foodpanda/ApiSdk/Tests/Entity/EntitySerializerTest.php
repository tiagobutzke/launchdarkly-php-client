<?php

namespace Foodpanda\ApiSdk\Tests\Entity;

use Foodpanda\ApiSdk\ApiFactory;
use Foodpanda\ApiSdk\Entity\Cms\CmsItemCollection;
use Foodpanda\ApiSdk\Entity\Vendor\VendorsCollection;
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

class EntitySerializerTest extends WebTestCase
{
    /**
     * @var ApiDataResponseFixtures
     */
    protected $apiDataResponseProvider;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function setUp()
    {
        $this->serializer = ApiFactory::createSerializer();
        $this->apiDataResponseProvider = new ApiDataResponseFixtures();
    }

    public function testGenerateCms()
    {
        $sourceData = $this->apiDataResponseProvider->getCmsResponseData();
        $entity = $this->serializer->denormalizeCms($sourceData);
        static::assertInstanceOf(CmsResults::class, $entity);
        static::assertInstanceOf(CmsItemCollection::class, $entity->getItems());

        $resultData = $this->serializer->normalize($entity);
        static::assertEquals($sourceData, $resultData);
    }

    public function testGenerateVendorList()
    {
        $sourceData = $this->apiDataResponseProvider->getVendorListResponseData();
        $entity = $this->serializer->denormalizeVendors($sourceData);
        static::assertInstanceOf(VendorsCollection::class, $entity->getItems());
        static::assertInstanceOf(VendorResults::class, $entity);

        $resultsData = $this->serializer->normalize($entity);
        static::assertEquals($sourceData, $resultsData);
    }

    public function testGenerateVendor()
    {
        $sourceData = $this->apiDataResponseProvider->getVendorResponseData();
        $entity = $this->serializer->denormalizeVendor($sourceData);
        $resultsData = $this->serializer->normalize($entity);
        $this->assertTheValidityOfVendorEntityStructure($entity);

        static::assertEquals($sourceData, $resultsData);
    }

    public function testGenerateConfiguration()
    {
        $sourceData = $this->apiDataResponseProvider->getConfigurationResponseData();
        $entity = $this->serializer->denormalizeConfiguration($sourceData);

        static::assertInstanceOf(SocialConnects::class, $entity->getEnabledSocialConnects());

        static::assertInstanceOf(
            FoodCharacteristicsCollection::class,
            $entity->getFoodCharacteristicAvailableFilters()
        );
        static::assertInstanceOf(FoodCharacteristics::class, $entity->getFoodCharacteristicAvailableFilters()->first());

        static::assertInstanceOf(LanguagesCollection::class, $entity->getLanguages());
        static::assertInstanceOf(Language::class, $entity->getLanguages()->first());

        // Customer Config
        static::assertInstanceOf(CustomerConfiguration::class, $entity->getCustomerConfiguration());
        static::assertInstanceOf(FormElementsCollection::class, $entity->getCustomerConfiguration()->getFormElements());
        static::assertInstanceOf(FormElement::class, $entity->getCustomerConfiguration()->getFormElements()->first());

        // Customer Address Config
        static::assertInstanceOf(CustomerAddressConfiguration::class, $entity->getCustomerAddressConfiguration());
        static::assertInstanceOf(
            FormElementsCollection::class,
            $entity->getCustomerAddressConfiguration()->getFormElements()
        );
        static::assertInstanceOf(
            FormElement::class,
            $entity->getCustomerAddressConfiguration()->getFormElements()->first()
        );

        // Payment Form Config
        static::assertInstanceOf(PaymentFormConfiguration::class, $entity->getPaymentFormConfiguration());
        static::assertInstanceOf(
            FormElementsCollection::class,
            $entity->getPaymentFormConfiguration()->getFormElements()
        );
        static::assertInstanceOf(
            FormElement::class,
            $entity->getPaymentFormConfiguration()->getFormElements()->first()
        );

        $resultData = $this->serializer->normalize($entity);

        static::assertEquals($sourceData, $resultData);
    }

    /**
     * @param Vendor $entity
     */
    protected function assertTheValidityOfVendorEntityStructure(Vendor $entity)
    {
        static::assertInstanceOf(Vendor::class, $entity);
        static::assertInstanceOf(MenusCollection::class, $entity->getMenus());

        /** @var Menu $menu */
        $menu = $entity->getMenus()->first();
        static::assertInstanceOf(Menu::class, $menu);
        static::assertInstanceOf(MenuCategoriesCollection::class, $menu->getMenuCategories());

        /** @var MenuCategory $menuCategory */
        $menuCategory = $menu->getMenuCategories()->first();
        static::assertInstanceOf(MenuCategory::class, $menuCategory);

        static::assertInstanceOf(ProductsCollection::class, $menuCategory->getProducts());

        /** @var Product $product */
        $product = $menuCategory->getProducts()->first();
        static::assertInstanceOf(Product::class, $product);
        static::assertInstanceOf(ProductVariationsCollection::class, $product->getProductVariations());

        /** @var ProductVariation $productVariation */
        $productVariation = $product->getProductVariations()->first();
        static::assertInstanceOf(ProductVariation::class, $productVariation);

        static::assertInstanceOf(Chain::class, $entity->getChain());
        static::assertInstanceOf(City::class, $entity->getCity());

        static::assertInstanceOf(CuisinesCollection::class, $entity->getCuisines());
        static::assertInstanceOf(Cuisine::class, $entity->getCuisines()->first());

        static::assertInstanceOf(MetaData::class, $entity->getMetadata());
        static::assertInstanceOf(EventsCollection::class, $entity->getMetadata()->getEvents());

        static::assertInstanceOf(SchedulesCollection::class, $entity->getSchedules());
        static::assertInstanceOf(Schedule::class, $entity->getSchedules()->first());

        static::assertInstanceOf(FoodCharacteristicsCollection::class, $entity->getFoodCharacteristics());
    }
}
