<?php

namespace Volo\EntityBundle\Tests\Unit\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Volo\EntityBundle\Service\EntityGenerator;
use Volo\EntityBundle\Service\EntityNormalizer;

class EntityGeneratorTest extends WebTestCase
{
    /**
     * @var EntityGenerator
     */
    protected $sut;

    /**
     * @var EntityNormalizer
     */
    protected $sut2;

    public function setUp()
    {
        $client = static::createClient();
        $this->sut = $client->getContainer()->get("volo_entity.service.entity_generator");
        $this->sut2 = $client->getContainer()->get("volo_entity.service.entity_normalizer");
    }

    public function testGenerateCms()
    {
        $jsonData = [
            "returned_count" => 1,
            "items" => [
                [
                    "id" => 549,
                    "code" => "Savour your favourite food with AXIS BANK",
                    "name" => "Good Food always leads to a Good Mood!",
                    "content" => "content"

                ]
            ]
        ];
        $entity = $this->sut->generateCms($jsonData);
        $resultData = $this->sut2->normalizeEntity($entity);
        $this->assertEquals($jsonData, $resultData);
    }
}
