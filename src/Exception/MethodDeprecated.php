<?php

namespace  Baytek\Laravel\Content\Exception;

use Exception;

class MethodDeprecated extends Exception
{
    /**
     * Create a content not found exception
     *
     * @param string $key The content key
     */
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}
