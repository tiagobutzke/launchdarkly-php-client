<?php

namespace Volo\FrontendBundle\Exception\Location;

class MissingKeysException extends \Exception
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
