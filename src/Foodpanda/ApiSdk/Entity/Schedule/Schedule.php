<?php

namespace Foodpanda\ApiSdk\Entity\Schedule;

use DateTime;
use Foodpanda\ApiSdk\Entity\DataObject;

class Schedule extends DataObject
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $weekday;

    /**
     * @var string
     */
    protected $opening_type;

    /**
     * @var DateTime
     */
    protected $opening_time;

    /**
     * @var DateTime
     */
    protected $closing_time;
}
