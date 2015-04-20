<?php

namespace Volo\EntityBundle\Entity\Language;

use Volo\EntityBundle\Entity\DataObject;

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
     * @param int $language_id
     */
    public function setLanguageId($language_id)
    {
        $this->language_id = $language_id;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * @param string $language_code
     */
    public function setLanguageCode($language_code)
    {
        $this->language_code = $language_code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
