<?php

namespace Snaccs\Builders;

use Elasticquent\ElasticquentInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class AbstractBuilder
 *
 * @package Snaccs\Builders
 */
abstract class AbstractBuilder
{
    protected array $data;

    protected ?Authenticatable $user;

    /**
     * The "outermost" builder will open a database transaction.
     *
     * @var bool
     */
    protected bool $opened_transaction = false;

    /**
     * Automatically index models after creating/updating.
     *
     * @var bool
     */
    protected bool $index = true;

    /**
     * AbstractBuilder constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * Set data.
     *
     * @param array $data
     *
     * @return self
     */
    public function setData(array $data = []): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param Authenticatable|null $user
     *
     * @return $this
     */
    public function setUser(?Authenticatable $user = null): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Call this in the constructor if this builder should never index.
     *
     * @return $this
     */
    public function disableIndexing(): static
    {
        $this->index = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function enableIndexing(): static
    {
        $this->index = true;

        return $this;
    }

    /**
     * This should use the $data array to create and save the model.
     *
     * @return Model|null
     */
    abstract protected function build();

    /**
     * Save within database transaction.
     *
     * @return Model|null
     */
    final public function save()
    {
        if (DB::transactionLevel() == 0) {
            $this->opened_transaction = true;

            DB::beginTransaction();
        }

        try {
            $entity = $this->build();

            // Commit and return entity
            if ($this->opened_transaction) {
                DB::commit();
            }

            if ($entity) {
                // Load any database column defaults, etc.
                $entity = $entity->fresh();

                // Note: if the elasticquent package is not installed (i.e. interface
                // does not exist), this will just be skipped without throwing an error.
                if ($this->index && $entity instanceof ElasticquentInterface) {
                    $entity->addToIndex();
                }
            }
        } catch (Throwable $e) {
            // Rollback if there's an exception
            if ($this->opened_transaction) {
                DB::rollBack();
            }

            throw $e;
        }

        $this->after($entity);

        return $entity;
    }

    /**
     * Most things would work better in an Observer's `created` method,
     * but you can handle any additional logic here if you like.
     *
     * @param Model|null $entity
     */
    protected function after($entity)
    {
        //
    }
}
