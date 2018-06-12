<?php

namespace Baytek\Laravel\Content\Traits;

use Crypt;

trait Encryptable
{
    /**
     * Get encrypted attribute
     *
     * @param  string $key They attribute key
     * @return string      Value
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, $this->encryptable)) {
            $value = Crypt::decrypt($value);
        }

        return $value;
    }

    /**
     * Set encrypted attribute
     *
     * @param  string $key   They attribute key
     * @param  string $value They attribute value
     * @return bool          Result of set
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable)) {
            $value = Crypt::encrypt($value);
        }

        return parent::setAttribute($key, $value);
    }
}
