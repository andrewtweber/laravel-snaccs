<?php

namespace Snaccs\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Job
 *
 * @package Snaccs\Models
 *
 * @property int    $id
 * @property string $queue
 * @property array  $payload
 * @property int    $attempts
 * @property Carbon $reserved_at
 * @property Carbon $available_at
 * @property Carbon $created_at
 */
class Job extends Model
{
    use SerializedJob;

    protected $table = 'jobs';

    public $timestamps = false;

    protected $casts = [
        'payload'      => 'json',
        'reserved_at'  => 'datetime',
        'available_at' => 'datetime',
        'created_at'   => 'datetime',
    ];
}
