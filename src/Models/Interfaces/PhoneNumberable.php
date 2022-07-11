<?php

namespace Snaccs\Models\Interfaces;

/**
 * Interface PhoneNumberable
 *
 * @package Snaccs\Models\Interfaces
 */
interface PhoneNumberable
{
    /**
     * @return string|null
     */
    public function getCountryCode(): ?string;
}
