<?php

namespace Snaccs\Mail;

use Carbon\Carbon;

/**
 * Interface Schedulable
 *
 * @package Snaccs\Mail
 */
interface Schedulable
{
    /**
     * @return string unique ID
     */
    public function uid(): string;

    /**
     * @return string
     */
    public function title(): string;

    /**
     * @return Carbon date and time
     */
    public function date(): Carbon;

    /**
     * @return int duration in minutes
     */
    public function duration(): int;

    /**
     * @return string address, etc.
     */
    public function location(): string;
}
