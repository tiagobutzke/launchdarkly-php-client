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
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getOpeningTime()
    {
        return $this->opening_time;
    }

    /**
     * @param string $opening_time
     */
    public function setOpeningTime($opening_time)
    {
        $this->opening_time = $opening_time;
    }

    /**
     * @return string
     */
    public function getClosingTime()
    {
        return $this->closing_time;
    }

    /**
     * @param string $closing_time
     */
    public function setClosingTime($closing_time)
    {
        $this->closing_time = $closing_time;
    }

    /**
     * @return MenuCategoriesCollection|MenuCategory[]
     */
    public function getMenuCategories()
    {
        return $this->menu_categories;
    }

    /**
     * @param MenuCategoriesCollection $menu_categories
     */
    public function setMenuCategories($menu_categories)
    {
        $this->menu_categories = $menu_categories;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}
