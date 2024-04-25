<?php

namespace RedJasmine\Order\UI\Http\Buyer\Api\Controller;


use Illuminate\Http\Request;
use RedJasmine\Order\Application\Services\OrderQueryService;
use RedJasmine\Order\Application\UserCases\Queries\OrderAllQuery;

class OrderController extends Controller
{
    public function __construct(protected readonly OrderQueryService $queryService)
    {
        $this->queryService->withQuery(function ($query) {
            $query->onlyBuyer($this->getOwner());
        });
    }


    public function index(Request $request)
    {
        // TODO request 转换
        $result = $this->queryService->paginate(OrderAllQuery::from([ 'query' => $request->query() ]));
        // TODO 转换 resources
        return $result;
    }

    public function store(Request $request)
    {
    }

    public function show(int $id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
