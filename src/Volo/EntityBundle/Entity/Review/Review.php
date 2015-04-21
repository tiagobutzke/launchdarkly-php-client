<?php

namespace Volo\EntityBundle\Entity\Review;

use Volo\EntityBundle\Entity\DataObject;

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
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getExternalReviewOpenriceScore()
    {
        return $this->external_review_openrice_score;
    }

    /**
     * @param int $external_review_openrice_score
     */
    public function setExternalReviewOpenriceScore($external_review_openrice_score)
    {
        $this->external_review_openrice_score = $external_review_openrice_score;
    }

    /**
     * @return string
     */
    public function getReviewExternalUrl()
    {
        return $this->review_external_url;
    }

    /**
     * @param string $review_external_url
     */
    public function setReviewExternalUrl($review_external_url)
    {
        $this->review_external_url = $review_external_url;
    }

    /**
     * @return string
     */
    public function getRatingType()
    {
        return $this->rating_type;
    }

    /**
     * @param string $rating_type
     */
    public function setRatingType($rating_type)
    {
        $this->rating_type = $rating_type;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
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
    public function getCustomerName()
    {
        return $this->customer_name;
    }

    /**
     * @param string $customer_name
     */
    public function setCustomerName($customer_name)
    {
        $this->customer_name = $customer_name;
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * @param int $customer_id
     */
    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;
    }

    /**
     * @return int
     */
    public function getVendorId()
    {
        return $this->vendor_id;
    }

    /**
     * @param int $vendor_id
     */
    public function setVendorId($vendor_id)
    {
        $this->vendor_id = $vendor_id;
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
}
