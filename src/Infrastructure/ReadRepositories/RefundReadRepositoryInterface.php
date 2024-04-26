<?php

namespace RedJasmine\Order\Infrastructure\ReadRepositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use RedJasmine\Order\Domain\Models\Order;
use RedJasmine\Order\Domain\Models\OrderRefund;
use RedJasmine\Support\Infrastructure\ReadRepositories\ReadRepositoryInterface;

interface RefundReadRepositoryInterface extends ReadRepositoryInterface
{

    public function findAll(array $query = []) : LengthAwarePaginator;

    public function findById($id, array $query = []) : OrderRefund;

}