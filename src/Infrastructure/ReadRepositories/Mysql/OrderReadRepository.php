<?php

namespace RedJasmine\Order\Infrastructure\ReadRepositories\Mysql;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use RedJasmine\Order\Domain\Models\Order;
use Spatie\QueryBuilder\QueryBuilder;


class OrderReadRepository extends QueryBuilder
{

    public function findAll(array $query = []) : LengthAwarePaginator
    {
        $request = new Request();
        $request->initialize($query);
        $this->resetRequest($request);
        return $this->paginate();
    }


    public function findById($id)
    {
        return $this->findOrFail($id);
    }


    public function queryCallbacks($queryCallbacks = []) : static
    {
        foreach ($queryCallbacks as $callback) {
            $callback($this);
        }
        return $this;
    }

    /**
     * @return EloquentBuilder
     */
    public function query() : static
    {
        return $this;
    }


    public function __construct($subject = null, ?Request $request = null)
    {
        parent::__construct(Order::query(), new Request());
    }

    /**
     * @param Request|null $request
     *
     * @return $this
     */
    public function resetRequest(?Request $request = null) : static
    {
        return $this->initializeRequest($request);
    }

}
