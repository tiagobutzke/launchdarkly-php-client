<?php

namespace Foodpanda\ApiSdk\Entity\Language;

use Foodpanda\ApiSdk\Entity\DataObject;

class Language extends DataObject
{
    /**
     * @var int
     */
    protected $language_id;

    /**
     * @var string
     */
    protected $language_code;

    /**
     * @var string
     */
    protected $name;

    /**
     * @return int
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
