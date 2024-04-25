<?php

namespace RedJasmine\Order\UI\Http\Buyer\Api\Controller;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RedJasmine\Order\Application\Services\OrderQueryService;
use RedJasmine\Order\Application\Services\OrderService;
use RedJasmine\Order\Application\UserCases\Commands\Others\OrderHiddenCommand;
use RedJasmine\Order\Application\UserCases\Queries\OrderAllQuery;
use RedJasmine\Order\UI\Http\Buyer\Api\Resources\OrderResource;

class OrderController extends Controller
{
    public function __construct(protected readonly OrderQueryService $queryService,
                                protected OrderService               $orderService,
    )
    {
        $this->queryService->withQuery(function ($query) {
            $query->onlyBuyer($this->getOwner());
        });
    }


    public function index(Request $request) : AnonymousResourceCollection
    {
        $result = $this->queryService->paginate(OrderAllQuery::from([ 'query' => $request->query() ]));
        return OrderResource::collection($result->appends($request->query()));
    }

    public function store(Request $request)
    {
        //TODO
    }

    public function show(Request $request, int $id)
    {
        $result = $this->queryService->find($id, $request->query());
        return OrderResource::make($result);
    }

    public function destroy($id) : JsonResponse
    {
        $this->queryService->find($id);

        $command = OrderHiddenCommand::from([ 'id' => $id ]);
        $this->orderService->buyerHidden($command);

        return static::success();
    }
}
