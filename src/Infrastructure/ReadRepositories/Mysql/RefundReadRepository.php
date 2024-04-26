<?php

namespace RedJasmine\Order\Infrastructure\ReadRepositories\Mysql;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\ClassString;
use phpDocumentor\Reflection\Types\This;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderRefund;
use RedJasmine\Order\Infrastructure\ReadRepositories\OrderReadRepositoryInterface;
use RedJasmine\Order\Infrastructure\ReadRepositories\RefundReadRepositoryInterface;
use RedJasmine\Support\Infrastructure\ReadRepositories\QueryBuilderReadRepository;
use Spatie\QueryBuilder\QueryBuilder;

class RefundReadRepository extends QueryBuilderReadRepository implements RefundReadRepositoryInterface
{
    /**
     * @var $modelClass class-string
     */
    protected string $modelClass = OrderRefund::class;


    public function findAll(array $query = []) : LengthAwarePaginator
    {
        return $this->query($query)->paginate();
    }

    public function findById($id, array $query = []) : OrderRefund
    {
        return $this->query($query)->findOrFail($id);
    }


}
