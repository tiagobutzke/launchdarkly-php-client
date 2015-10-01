<?php

namespace Volo\FrontendBundle\Service;

use Foodpanda\ApiSdk\Entity\Customer\Customer;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpFoundation\Response;

class SpecialInstructionsTutorialService
{
    const DISABLE_TUTORIAL_BACKEND_KEY = 'user-%s-hide-special-instructions-tutorial';
    const DISABLE_TUTORIAL_COOKIE_KEY = 'hide_special_instructions_tutorial';

    /**
     * @var CustomerService
     */
    private $customerService;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param CustomerService $customerService
     * @param Cache $cache
     */
    public function __construct(CustomerService $customerService, Cache $cache) {
        $this->customerService = $customerService;
        $this->cache = $cache;
    }

    /**
     * @param Customer $customer
     *
     * @return bool
     */
    public function isTutorialEnabledForCustomer(Customer $customer) {
        return !$this->cache->contains($this->createCacheKey($customer));
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isTutorialEnabledForGuest(Request $request) {
        return !$request->cookies->has(static::DISABLE_TUTORIAL_COOKIE_KEY);
    }

    /**
     * @param Customer $customer
     */
    public function disableTutorialForCustomer(Customer $customer) {
        $this->cache->save($this->createCacheKey($customer), true);
    }

    /**
     * @param Response $response
     */
    public function disableTutorialForGuest(Response $response) {
        $cookie = new Cookie(static::DISABLE_TUTORIAL_COOKIE_KEY, '1');
        $response->headers->setCookie($cookie);
    }

    /**
     * @param Customer $customer
     *
     * @return string
     */
    private function createCacheKey(Customer $customer)
    {
        return sprintf(static::DISABLE_TUTORIAL_BACKEND_KEY, $customer->getId());
    }
}
