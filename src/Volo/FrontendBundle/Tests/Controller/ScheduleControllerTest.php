<?php

namespace Volo\FrontendBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Volo\FrontendBundle\Tests\VoloTestCase;

class ScheduleControllerTest extends VoloTestCase
{
    public function testGetTimes()
    {
        $client = static::createClient();

        $client->request('GET', '/api/v1/schedules/684');

        $response = $client->getResponse();
        $this->isSuccessful($response);
        $data = json_decode($response->getContent(), true);

        $this->assertCount(3, $data);
        foreach ($data as $day) {
            $this->assertArrayHasKey('text', $day);
            $this->assertArrayHasKey('times', $day);
            $this->assertNotEmpty($day['times']);
        }
    }
}
