<?php

namespace Foodpanda\ApiSdk\Entity\Review;

use Foodpanda\ApiSdk\Entity\DataObject;

class Review extends DataObject
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var int
     */
    protected $vendor_id;

    /**
     * @var int
     */
    protected $customer_id;

    /**
     * @var string
     */
    protected $customer_name;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var int
     */
    protected $rating;

    /**
     * @var string
     */
    protected $date;

    /**
     * @var string
     */
    protected $rating_type;

    /**
     * @var string
     */
    protected $review_external_url;

    /**
     * @var int
     */
    protected $external_review_openrice_score;

    /**
     * @var string
     */
    protected $title;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getExternalReviewOpenriceScore()
    {
        return $this->external_review_openrice_score;
    }

    /**
     * @return string
     */
    public function getReviewExternalUrl()
    {
        return $this->review_external_url;
    }

    /**
     * @return string
     */
    public function getRatingType()
    {
        return $this->rating_type;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
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
    public function getCustomerName()
    {
        return $this->customer_name;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @return int
     */
    public function getVendorId()
    {
        return $this->vendor_id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
