<?php

namespace Volo\FrontendBundle\Tests\Twig;

use Volo\FrontendBundle\Tests\VoloTestCase;
use Volo\FrontendBundle\Twig\OrderExtension;

class OrderExtensionTest extends VoloTestCase
{
    private $currentTimezone;

    protected function setUp()
    {
        parent::setUp();

        $this->currentTimezone = date_default_timezone_get();

        // Random timezone
        date_default_timezone_set('Australia/Sydney');
    }

    protected function tearDown()
    {
        parent::tearDown();

        date_default_timezone_set($this->currentTimezone);
    }


    /**
     * @param int $position
     * @param array $data
     * @param bool $isActive
     *
     * @dataProvider dataProvider
     */
    public function testActivateStepTracking($position, $data, $isActive)
    {
        $this->markTestSkipped('API changed');

        $cssClassName = 'tracking-step-active';
        $expectedClass = $isActive ? $cssClassName : '';

        /** @var \DateTime $now */
        $now    = $data['now'];
        $status = $data['status'];

        $orderExtension = new OrderExtension();
        $result = $orderExtension->activateStepTracking($cssClassName, $position, $status);

        $message = sprintf(
            'for status code "%d" and position "%d" css class should be "%s". Now "%s", changedAt: "%s".',
            $status['display_status']['code'],
            $position,
            $expectedClass,
            $now,
            $status['status_history'][0]['changedAt']['date']
        );
        $this->assertEquals($expectedClass, $result, $message);
    }

    public function dataProvider()
    {
        $return = [];

        $code = 2;
        $status = $this->createStatus($code);
        $return = array_merge($return, [
            [1, $status, false],
            [2, $status, false],
            [3, $status, false],
            [4, $status, false],
            [5, $status, false],
        ]);

        $code = 3;
        $status = $this->createStatus($code);
        $return = array_merge($return, [
            [1, $status, false],
            [2, $status, false],
            [3, $status, false],
            [4, $status, false],
            [5, $status, false],
        ]);

        $code = 4;
        $status = $this->createStatus($code);
        $return = array_merge($return, [
            [1, $status, false],
            [2, $status, false],
            [3, $status, false],
            [4, $status, false],
            [5, $status, false],
        ]);

        $code = 11;
        $status = $this->createStatus($code);
        $return = array_merge($return, [
            [1, $status, true],
            [2, $status, false],
            [3, $status, false],
            [4, $status, false],
            [5, $status, false],
        ]);

        /* Fake status */
        $code = 11;
        $status = $this->createStatus($code, true);
        $return = array_merge($return, [
            [1, $status, true],
            [2, $status, true],
            [3, $status, false],
            [4, $status, false],
            [5, $status, false],
        ]);

        $code = 14;
        $status = $this->createStatus($code);
        $return = array_merge($return, [
            [1, $status, true],
            [2, $status, true],
            [3, $status, true],
            [4, $status, false],
            [5, $status, false],
        ]);

        $code = 15;
        $status = $this->createStatus($code);
        $return = array_merge($return, [
            [1, $status, true],
            [2, $status, true],
            [3, $status, true],
            [4, $status, true],
            [5, $status, false],
        ]);

        $code = 16;
        $status = $this->createStatus($code);
        $return = array_merge($return, [
            [1, $status, true],
            [2, $status, true],
            [3, $status, true],
            [4, $status, true],
            [5, $status, true],
        ]);

        return $return;
    }

    protected function createStatus($code, $isFake = false)
    {
        $now = new \DateTime('now', new \DateTimeZone('Europe/Berlin'));
        $lastStatusUpdate = new \DateTime('now', new \DateTimeZone('Europe/Berlin'));
        if ($isFake) {
            $lastStatusUpdate->sub(\DateInterval::createFromDateString('+2 minute +1 second'));
        }

        return [
            'now'    => $now->format('Y-m-d H:i:s.u'),
            'status' => [
                'status_history' => [
                    [
                        'code' => $code,
                        'message' => 'Order accepted by vendor',
                        'type' => 'final',
                        'changedAt' => [
                            'date' => $lastStatusUpdate->format('Y-m-d H:i:s.u'),
                            'timezone_type' => 3,
                            'timezone' => 'Europe/Berlin',
                        ],
                    ],
                    [
                        'code' => 4,
                        'message' => 'shop.order.status.message_payment_process',
                        'type' => 'temporary',
                        'changedAt' => [
                            'date' => '2015-06-10 17:17:14.000000',
                            'timezone_type' => 3,
                            'timezone' => 'Europe/Berlin',
                        ],
                    ],
                    [
                        'code' => 3,
                        'message' => 'Preorder awaiting specified delivery time',
                        'type' => 'temporary',
                        'changedAt' => [
                            'date' => '2015-06-10 17:15:42.000000',
                            'timezone_type' => 3,
                            'timezone' => 'Europe/Berlin',
                        ],
                    ],
                    [
                        'code' => 2,
                        'message' => 'Awaiting confirmation from vendor',
                        'type' => 'temporary',
                        'changedAt' => [
                            'date' => '2015-06-10 16:49:43.000000',
                            'timezone_type' => 3,
                            'timezone' => 'Europe/Berlin',
                        ],
                    ],
                    [
                        'code' => 4,
                        'message' => 'shop.order.status.message_payment_process',
                        'type' => 'temporary',
                        'changedAt' => [
                            'date' => '2015-06-10 16:49:37.000000',
                            'timezone_type' => 3,
                            'timezone' => 'Europe/Berlin',
                        ],
                    ],
                ],
                'order_address' => [
                    'id' => 132141,
                    'city' => 'Berlin',
                    'area' => 'Berlin',
                    'address_lines' => [0 => 'Alexanderplatz',],
                    'address_other' => null,
                    'room' => null,
                    'flatNumber' => null,
                    'structure' => null,
                    'building' => null,
                    'intercom' => null,
                    'entrance' => null,
                    'floor' => null,
                    'district' => null,
                    'postcode' => '10117',
                    'company' => '',
                    'latitude' => null,
                    'longitude' => null,
                ],
                'order_id' => 124,
                'order_code' => 's9iz-x8wg',
                'display_status' => null,
                'ordered_at' => [
                    'date' => '2015-06-10 16:49:34.000000',
                    'timezone_type' => 3,
                    'timezone' => 'Europe/Berlin',
                ],
                'confirmed_delivery_time' => [
                    'date' => '2015-06-10 17:34:34.000000',
                    'timezone_type' => 3,
                    'timezone' => 'Europe/Berlin',
                ],
                'order_products' => [
                    'name' => 'Pizza Margherita',
                    'total_price' => 14
                ],
                'total_value' => 14,
                'internal_status_code' => null,
            ],
        ];
    }
}
