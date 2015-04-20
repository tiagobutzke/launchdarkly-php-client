<?php

namespace Volo\EntityBundle\Entity\FoodCharacteristics;

use Volo\EntityBundle\Entity\DataObject;

class FoodCharacteristics extends DataObject
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $is_halal;

    /**
     * @var bool
     */
    protected $is_vegetarian;

    /**
     * @var bool
     */
    protected $is_mobile_filter;
}
