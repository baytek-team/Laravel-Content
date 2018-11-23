<?php

namespace  Baytek\Laravel\Content\Exception;

use Exception;

class TypeNotSupported extends Exception
{
    /**
     * Create a content not found exception
     *
     * @param string $key The content key
     */
    public function __construct($message, $code = 0, Exception $previous = null) {

        // make sure everything is assigned properly
        parent::__construct('Type is not supported', $code, $previous);
    }
}
