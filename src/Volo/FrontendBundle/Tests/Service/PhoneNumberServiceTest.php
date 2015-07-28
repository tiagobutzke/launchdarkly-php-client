<?php
/**
 * Created by PhpStorm.
 * User: Shehabic
 * Date: 28/07/15
 * Time: 10:45
 */

namespace Volo\FrontendBundle\Tests\Service;

use Volo\FrontendBundle\Service\PhoneNumberService;
use Volo\FrontendBundle\Tests\VoloTestCase;

class PhoneNumberServiceTest extends VoloTestCase
{
    /**
     * @dataProvider numbersFixture
     * @param array $data
     */
    public function testValidPhoneNumber($data)
    {
        $client = static::createClient();
        $service = new PhoneNumberService(
            \libphonenumber\PhoneNumberUtil::getInstance(),
            $data['cc'],
            $client->getContainer()->get('translator')
        );

        $parsedNumber = $service->parsePhoneNumber($data['num']);
        $this->assertEquals($data['parsed_code'], $parsedNumber->getCountryCode());
        $this->assertEquals($data['parsed_number'], $parsedNumber->getNationalNumber());
        $service->validateNumber($parsedNumber);
    }

    /**
     * @return array
     */
    public function numbersFixture()
    {
        return [
            // Germany Mobile
            [['cc' => 'DE', 'num' => '017675514433',    'parsed_code' => '49', 'parsed_number' => '17675514433']],
            [['cc' => 'DE', 'num' => '17675517788',     'parsed_code' => '49', 'parsed_number' => '17675517788']],
            [['cc' => 'DE', 'num' => '+4917675519910',  'parsed_code' => '49', 'parsed_number' => '17675519910']],
            [['cc' => 'DE', 'num' => '004917675519910', 'parsed_code' => '49', 'parsed_number' => '17675519910']],

            // Germany Land-Line
            [['cc' => 'DE', 'num' => '089 55276787',     'parsed_code' => '49', 'parsed_number' => '8955276787']],
            [['cc' => 'DE', 'num' => '89 55276787',      'parsed_code' => '49', 'parsed_number' => '8955276787']],
            [['cc' => 'DE', 'num' => '+49 89 55276787 ', 'parsed_code' => '49', 'parsed_number' => '8955276787']],
            [['cc' => 'DE', 'num' => '0049 89 55276787', 'parsed_code' => '49', 'parsed_number' => '8955276787']],

            // France Mobile
            [['cc' => 'FR', 'num' => '0690332211',    'parsed_code' => '33', 'parsed_number' => '690332211']],
            [['cc' => 'FR', 'num' => '690332211',     'parsed_code' => '33', 'parsed_number' => '690332211']],
            [['cc' => 'FR', 'num' => '+33690332211',  'parsed_code' => '33', 'parsed_number' => '690332211']],
            [['cc' => 'FR', 'num' => '0033690332211', 'parsed_code' => '33', 'parsed_number' => '690332211']],

            // France Land-Line
            [['cc' => 'FR', 'num' => '01 47 70 38 06',    'parsed_code' => '33', 'parsed_number' => '147703806']],
            [['cc' => 'FR', 'num' => '147703806',         'parsed_code' => '33', 'parsed_number' => '147703806']],
            [['cc' => 'FR', 'num' => '+33 1477 03806 ',   'parsed_code' => '33', 'parsed_number' => '147703806']],
            [['cc' => 'FR', 'num' => '0033 147 70 38 06', 'parsed_code' => '33', 'parsed_number' => '147703806']],

            // Norway Mobile
            [['cc' => 'NO', 'num' => '59 77 03 80',    'parsed_code' => '47', 'parsed_number' => '59770380']],
              // Norway doesn't have "0" prefix for local numbers from city to city..
            [['cc' => 'NO', 'num' => '+47 5977 0380 ',   'parsed_code' => '47', 'parsed_number' => '59770380']],
            [['cc' => 'NO', 'num' => '0047 597 70 38 0', 'parsed_code' => '47', 'parsed_number' => '59770380']],

            // Norway Land-Line
            [['cc' => 'NO', 'num' => '23629010',     'parsed_code' => '47', 'parsed_number' => '23629010']],
            // Norway doesn't have "0" prefix for local numbers from city to city..
            [['cc' => 'NO', 'num' => '+4723629010',  'parsed_code' => '47', 'parsed_number' => '23629010']],
            [['cc' => 'NO', 'num' => '004723629010', 'parsed_code' => '47', 'parsed_number' => '23629010']],

            // Sweden Mobile
            [['cc' => 'SE', 'num' => '07013 12345',     'parsed_code' => '46', 'parsed_number' => '701312345']],
            [['cc' => 'SE', 'num' => '7013-12345',      'parsed_code' => '46', 'parsed_number' => '701312345']],
            [['cc' => 'SE', 'num' => '+46-7013-12345',  'parsed_code' => '46', 'parsed_number' => '701312345']],
            [['cc' => 'SE', 'num' => '0046 7013 12345', 'parsed_code' => '46', 'parsed_number' => '701312345']],

            // Sweden Land-Line
            [['cc' => 'SE', 'num' => '46-812 34 56',     'parsed_code' => '46', 'parsed_number' => '468123456']],
            [['cc' => 'SE', 'num' => '0468123456',       'parsed_code' => '46', 'parsed_number' => '468123456']],
            [['cc' => 'SE', 'num' => '+46 0468123456',   'parsed_code' => '46', 'parsed_number' => '468123456']],
            [['cc' => 'SE', 'num' => '0046 46 812 3456', 'parsed_code' => '46', 'parsed_number' => '468123456']],

        ];
    }
}
