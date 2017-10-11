<?php

namespace  Baytek\Laravel\Content\Exception;

use Exception;

class ContentNotFoundException extends Exception
//implements ExceptionInterface
{
    public function __construct($key)
    {
    	$this->message = "The content key '{$key}' was not found.";
    }
}
