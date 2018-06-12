<?php

namespace  Baytek\Laravel\Content\Exception;

use Exception;

class ContentNotFoundException extends Exception
//implements ExceptionInterface
{
    /**
     * Create a content not found exception
     *
     * @param string $key The content key
     */
    public function __construct($key)
    {
    	$this->message = "The content key '{$key}' was not found.";
    }
}
