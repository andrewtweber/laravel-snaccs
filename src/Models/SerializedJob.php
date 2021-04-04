<?php

namespace Snaccs\Models;

use Illuminate\Mail\SendQueuedMailable;

/**
 * Trait SerializedJob
 *
 * @package Snaccs\Models
 *
 * @property string $type
 * @property mixed  $command
 * @property bool   $is_mail
 * @property array  $to
 */
trait SerializedJob
{
    /**
     * @return string
     */
    public function getTypeAttribute()
    {
        return class_basename($this->payload['displayName']);
    }

    /**
     * @return mixed
     */
    public function getCommandAttribute()
    {
        return unserialize($this->payload['data']['command']);
    }

    /**
     * @return bool
     */
    public function getIsMailAttribute()
    {
        return $this->command instanceof SendQueuedMailable;
    }

    /**
     * @return array|null
     */
    public function getToAttribute()
    {
        if (! $this->is_mail) {
            return null;
        }

        return $this->command->mailable->to;
    }
}
