<?php

namespace Foodpanda\ApiSdk\Entity\Menu;

use Foodpanda\ApiSdk\Entity\DataObject;
use Foodpanda\ApiSdk\Entity\MenuCategory\MenuCategoriesCollection;
use Foodpanda\ApiSdk\Entity\MenuCategory\MenuCategory;

class Menu extends DataObject
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
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $opening_time;

    /**
     * @var string
     */
    protected $closing_time;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var MenuCategoriesCollection
     */
    protected $menu_categories;

    public function __construct()
    {
        $this->menu_categories = new MenuCategoriesCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getOpeningTime()
    {
        return $this->opening_time;
    }

    /**
     * @return string
     */
    public function getClosingTime()
    {
        return $this->closing_time;
    }

    /**
     * @return MenuCategoriesCollection|MenuCategory[]
     */
    public function getMenuCategories()
    {
        return $this->menu_categories;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
