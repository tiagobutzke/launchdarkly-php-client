<?php

namespace Volo\EntityBundle\Tests\Inte\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Volo\EntityBundle\Entity\Chain\Chain;
use Volo\EntityBundle\Entity\City\City;
use Volo\EntityBundle\Entity\Configuration\PaymentFormConfiguration;
use Volo\EntityBundle\Entity\Configuration\SocialConnects;
use Volo\EntityBundle\Entity\Cuisine\Cuisine;
use Volo\EntityBundle\Entity\Cuisine\CuisinesCollection;
use Volo\EntityBundle\Entity\Customer\CustomerAddressConfiguration;
use Volo\EntityBundle\Entity\Customer\CustomerConfiguration;
use Volo\EntityBundle\Entity\Event\EventsCollection;
use Volo\EntityBundle\Entity\FoodCharacteristics\FoodCharacteristics;
use Volo\EntityBundle\Entity\FoodCharacteristics\FoodCharacteristicsCollection;
use Volo\EntityBundle\Entity\FormElement\FormElement;
use Volo\EntityBundle\Entity\FormElement\FormElementsCollection;
use Volo\EntityBundle\Entity\Language\Language;
use Volo\EntityBundle\Entity\Language\LanguagesCollection;
use Volo\EntityBundle\Entity\Menu\Menu;
use Volo\EntityBundle\Entity\Menu\MenusCollection;
use Volo\EntityBundle\Entity\MenuCategory\MenuCategoriesCollection;
use Volo\EntityBundle\Entity\MenuCategory\MenuCategory;
use Volo\EntityBundle\Entity\Product\Product;
use Volo\EntityBundle\Entity\Product\ProductsCollection;
use Volo\EntityBundle\Entity\ProductVariation\ProductVariation;
use Volo\EntityBundle\Entity\ProductVariation\ProductVariationsCollection;
use Volo\EntityBundle\Entity\Schedule\Schedule;
use Volo\EntityBundle\Entity\Schedule\SchedulesCollection;
use Volo\EntityBundle\Entity\Vendor\MetaData;
use Volo\EntityBundle\Entity\Vendor\Vendor;
use Volo\EntityBundle\Entity\Vendor\VendorResults;
use Volo\EntityBundle\Service\EntityGenerator;
use Volo\EntityBundle\Service\EntityNormalizer;
use Volo\EntityBundle\Tests\Fixtures\ApiDataResponseFixtures;

class EntityGeneratorTest extends WebTestCase
{
    /**
     * @var ApiDataResponseFixtures
     */
    protected $apiDataResponseProvider;

    /**
     * @var EntityGenerator
     */
    protected $entityGenerator;

    /**
     * @var EntityNormalizer
     */
    protected $entityNormalizer;

    public function setUp()
    {
        $client = static::createClient();
        $this->entityGenerator = $client->getContainer()->get('volo_entity.service.entity_generator');
        $this->entityNormalizer = $client->getContainer()->get('volo_entity.service.entity_normalizer');
        $this->apiDataResponseProvider = new ApiDataResponseFixtures();
    }

    public function testGenerateCms()
    {
        $sourceData = $this->apiDataResponseProvider->getCmsResponseData();
        $entity = $this->entityGenerator->generateCms($sourceData);
        $resultData = $this->entityNormalizer->normalizeEntity($entity);

        static::assertEquals($sourceData, $resultData);
    }

    public function testGenerateVendorList()
    {
        $sourceData = $this->apiDataResponseProvider->getVendorListResponseData();
        $entity = $this->entityGenerator->generateVendors($sourceData);
        $resultsData = $this->entityNormalizer->normalizeEntity($entity);
        static::assertInstanceOf(VendorResults::class, $entity);

        static::assertEquals($sourceData, $resultsData);
    }

    public function testGenerateVendor()
    {
        $sourceData = $this->apiDataResponseProvider->getVendorResponseData();
        $entity = $this->entityGenerator->generateVendor($sourceData);
        $resultsData = $this->entityNormalizer->normalizeEntity($entity);
        $this->assertTheValidityOfVendorEntityStructure($entity);

        static::assertEquals($sourceData, $resultsData);
    }

    public function testGenerateConfiguration()
    {
        $sourceData = $this->apiDataResponseProvider->getConfigurationResponseData();
        $entity = $this->entityGenerator->generateConfiguration($sourceData);
        $resultData = $this->entityNormalizer->normalizeEntity($entity);

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
