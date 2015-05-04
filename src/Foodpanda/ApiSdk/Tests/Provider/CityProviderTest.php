<?php

namespace Foodpanda\ApiSdk\Tests\Provider;

use Foodpanda\ApiSdk\ApiFactory;
use Foodpanda\ApiSdk\Entity\City\City;
use Foodpanda\ApiSdk\Entity\City\CityResults;
use Foodpanda\ApiSdk\Exception\ApiException;
use Foodpanda\ApiSdk\Provider\CityProvider;
use Foodpanda\ApiSdk\Tests\ApiSdkTestSuite;

class CityProviderTest extends ApiSdkTestSuite
{
    protected function setUp()
    {
        parent::setUp();

        ApiFactory::setOptionFilename(__DIR__ . '/../config_test.php');
    }

    public function testCreateInstance()
    {
        $cityProvider = CityProvider::createInstance();

        $this->assertInstanceOf(CityProvider::class, $cityProvider);
    }

    public function testFindAll()
    {
        $cities = CityProvider::createInstance()->findAll();

        $this->assertInstanceOf(CityResults::class, $cities);
        $this->assertEquals(167, $cities->getItems()->count());
    }

    public function testFindWithValidId()
    {
        static::assertInstanceOf(City::class, CityProvider::createInstance()->find(5));
    }

    public function testFindWithInvalidId()
    {
        $this->setExpectedException(ApiException::class);

        CityProvider::createInstance()->find(99999);
    }
}
