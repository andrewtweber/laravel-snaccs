<?php

namespace Snaccs\Mail;

use Illuminate\Support\Facades\Storage;

/**
 * Class Attachment
 *
 * @package Snaccs\Mail
 */
abstract class Attachment
{
    /**
     * @return string
     */
    abstract public function filename(): string;

    /**
     * @return string
     */
    abstract public function contents(): string;

    /**
     * @return string
     */
    abstract public function mimetype(): string;

    /**
     * @return string path to file
     */
    public function toFile(): string
    {
        $filename = 'attachments/' . $this->filename();

        Storage::put($filename, $this->contents());

        return storage_path('app/' . $filename);
    }
}
