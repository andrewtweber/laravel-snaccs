<?php

namespace Snaccs\Fractal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use League\Fractal\Resource\Collection as CollectionResource;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

/**
 * Class EloquentTransformer
 *
 * Distinguishes between null item (`null`) and null collection (empty array)
 * Simplifies relationships - handle null and default to a `toArray` transformer
 *
 * @package Snaccs\Fractal
 */
class EloquentTransformer extends TransformerAbstract
{
    /**
     * @return Primitive
     */
    public function nullItem()
    {
        return $this->primitive(null);
    }

    /**
     * @return Primitive
     */
    public function nullCollection()
    {
        return $this->primitive([]);
    }

    /**
     * @param Model|null                   $model
     * @param TransformerAbstract|callable $transformer
     * @param string|null                  $resourceKey
     *
     * @return Item|Primitive
     */
    public function belongsTo(?Model $model, $transformer = null, ?string $resourceKey = null)
    {
        if ($model === null) {
            return $this->nullItem();
        }

        if ($transformer !== null) {
            return $this->item($model, $transformer, $resourceKey);
        }

        return $this->item($model, function (Model $model) {
            return $model->toArray();
        }, $resourceKey);
    }

    /**
     * @param Model|null                   $model
     * @param TransformerAbstract|callable $transformer
     * @param string|null                  $resourceKey
     *
     * @return Item|NullResource
     */
    public function hasOne(?Model $model, $transformer = null, ?string $resourceKey = null)
    {
        return $this->belongsTo($model, $transformer, $resourceKey);
    }

    /**
     * @param Model|null                   $model
     * @param TransformerAbstract|callable $transformer
     * @param string|null                  $resourceKey
     *
     * @return Item|NullResource
     */
    public function morphTo(?Model $model, $transformer = null, ?string $resourceKey = null)
    {
        return $this->belongsTo($model, $transformer, $resourceKey);
    }

    /**
     * @param Model|null                   $model
     * @param TransformerAbstract|callable $transformer
     * @param string|null                  $resourceKey
     *
     * @return Item|NullResource
     */
    public function morphOne(?Model $model, $transformer = null, ?string $resourceKey = null)
    {
        return $this->belongsTo($model, $transformer, $resourceKey);
    }

    /**
     * @param Collection|null              $models
     * @param TransformerAbstract|callable $transformer
     * @param string|null                  $resourceKey
     *
     * @return CollectionResource|Primitive
     */
    public function hasMany(?Collection $models, $transformer = null, ?string $resourceKey = null)
    {
        if ($models === null) {
            return $this->nullCollection();
        }

        if ($transformer !== null) {
            return $this->collection($models, $transformer, $resourceKey);
        }

        return $this->collection($models, function (Model $model) {
            return $model->toArray();
        }, $resourceKey);
    }

    /**
     * @param Collection|null              $models
     * @param TransformerAbstract|callable $transformer
     * @param string|null                  $resourceKey
     *
     * @return CollectionResource|NullResource
     */
    public function belongsToMany(?Collection $models, $transformer = null, ?string $resourceKey = null)
    {
        return $this->hasMany($models, $transformer, $resourceKey);
    }

    /**
     * @param Collection|null              $models
     * @param TransformerAbstract|callable $transformer
     * @param string|null                  $resourceKey
     *
     * @return CollectionResource|NullResource
     */
    public function morphMany(?Collection $models, $transformer = null, ?string $resourceKey = null)
    {
        return $this->hasMany($models, $transformer, $resourceKey);
    }
}
