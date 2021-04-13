<?php namespace Snaccs\Builders;

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
     * This should use the $data array to create and save the model.
     *
     * @return Model
     */
    abstract protected function build();

    /**
     * Save within database transaction.
     *
     * @return Model
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

            // Load any database column defaults, etc.
            $entity = $entity->fresh();

            if ($entity instanceof ElasticquentInterface) {
                $entity->addToIndex();
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
     * @param Model $entity
     */
    protected function after($entity)
    {
        //
    }
}
