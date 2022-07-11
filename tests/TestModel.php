<?php

namespace Snaccs\Tests;

use Illuminate\Database\Eloquent\Model;
use Snaccs\Models\Interfaces\PhoneNumberable;

/**
 * Class TestModel
 *
 * @package Snaccs\Tests
 */
class TestModel extends Model implements PhoneNumberable
{
    public ?string $country_code = null;

    /**
     * @param string|null $country_code
     */
    public function setCountryCode(?string $country_code)
    {
        $this->country_code = $country_code;
    }

    /**
     * @return string|null
     */
    public function getCountryCode(): ?string
    {
        return $this->country_code;
    }
}
