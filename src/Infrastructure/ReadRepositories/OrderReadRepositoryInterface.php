<?php

namespace RedJasmine\Order\Infrastructure\ReadRepositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Support\Infrastructure\ReadRepositories\ReadRepositoryInterface;

interface OrderReadRepositoryInterface extends ReadRepositoryInterface
{

    public function findAll(array $query = []) : LengthAwarePaginator;

    public function findById($id, array $query = []) : Order;

}
