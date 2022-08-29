<?php

namespace Snaccs\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

/**
 * Class FailedJob
 *
 * @package Snaccs\Models
 *
 * @property int        $id
 * @property string     $connection
 * @property string     $queue
 * @property array      $payload
 * @property HtmlString $exception
 * @property Carbon     $failed_at
 */
class FailedJob extends Model
{
    use SerializedJob;

    protected $table = 'failed_jobs';

    public $timestamps = false;

    protected $casts = [
        'payload' => 'json',
    ];

    protected $dates = ['failed_at'];

    /**
     * @param string $value
     *
     * @return HtmlString
     */
    public function getExceptionAttribute($value)
    {
        [$value,] = explode('Stack trace:', $value);

        return new HtmlString(nl2br(e($value)));
    }
}
