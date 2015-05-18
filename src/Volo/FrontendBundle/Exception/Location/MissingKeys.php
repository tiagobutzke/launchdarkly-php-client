<?php

namespace Volo\FrontendBundle\Exception\Location;

use RuntimeException;

class MissingKeys extends RuntimeException
{
    /**
     * @param array $keys
     */
    public function __construct(array $keys)
    {
        $message = sprintf('Some keys for an user location are missing: %s', implode(',', $keys));
        parent::__construct($message, 0, null);
    }
}
