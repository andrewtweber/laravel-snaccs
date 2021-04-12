<?php

namespace Snaccs\Hashids;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Trait HashedID
 *
 * @package Snaccs\Hashids
 */
trait HashedID
{
    /**
     * Retrieve the model for a bound value.
     *
     * @param mixed       $value
     * @param string|null $field
     *
     * @return Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return static::findByDisplayIdOrFail($value);
    }

    /**
     * Encoded display ID.
     *
     * @return string
     */
    public function getDisplayIdAttribute()
    {
        $connection = config('hashids.default');
        if (property_exists(static::class, 'hashids_connection')) {
            $connection = static::$hashids_connection;
        }

        return Hashids::connection($connection)->encode($this->id);
    }

    /**
     * Find by display ID
     *
     * @param string $display_id
     *
     * @return static|null
     */
    public static function findByDisplayId($display_id)
    {
        $connection = config('hashids.default');
        if (property_exists(static::class, 'hashids_connection')) {
            $connection = static::$hashids_connection;
        }

        $decoded_id = Hashids::connection($connection)->decode($display_id);

        if (empty($decoded_id)) {
            return null;
        }

        return static::find($decoded_id[0]);
    }

    /**
     * Find by display ID or fail
     *
     * @param string $display_id
     *
     * @return static|null
     */
    public static function findByDisplayIdOrFail($display_id)
    {
        $connection = config('hashids.default');
        if (property_exists(static::class, 'hashids_connection')) {
            $connection = static::$hashids_connection;
        }

        $decoded_id = Hashids::connection($connection)->decode($display_id);

        if (empty($decoded_id)) {
            throw new ModelNotFoundException();
        }

        return static::findOrFail($decoded_id[0]);
    }
}
