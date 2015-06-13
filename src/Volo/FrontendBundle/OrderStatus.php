<?php

namespace Volo\FrontendBundle;

final class OrderStatus
{
    /**
     * The key is the "user message code", the value is the tracking step number to activate
     *
     * @var array
     */
    public static $userCodeMapping = [
        2  => [],
        3  => [],
        4  => [],
        11 => [1],
        12 => [],
        13 => [],
        0  => [1, 2], // Fake status, Will be implemented once Foodora has the capacity to give feedback through printers.
        14 => [1, 2, 3],
        15 => [1, 2, 3, 4],
        16 => [1, 2, 3, 4, 5],
    ];
}
