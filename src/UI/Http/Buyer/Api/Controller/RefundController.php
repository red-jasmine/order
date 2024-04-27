<?php

namespace RedJasmine\Order\UI\Http\Buyer\Api\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RedJasmine\Order\Application\Services\OrderCommandService;
use RedJasmine\Order\Application\Services\RefundCommandService;
use RedJasmine\Order\Application\Services\RefundQueryService;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;
use RedJasmine\Order\UI\Http\Buyer\Api\Resources\OrderRefundResource;

class RefundController extends Controller
{

    public function __construct(protected readonly RefundQueryService $queryService,
                                protected RefundCommandService        $commandService,
                                protected OrderCommandService         $orderCommandService,
    )
    {
        $this->commandService->setOperator(function () {
            return $this->getUser();
        });
        $this->queryService->withQuery(function ($query) {
            $query->onlyBuyer($this->getOwner());
        });

        $this->orderCommandService->setOperator(function () {
            return $this->getUser();
        });

    }

    public function index(Request $request) : AnonymousResourceCollection
    {
        $result = $this->queryService->paginate($request->query());
        return OrderRefundResource::collection($result);
    }

    public function store(Request $request) : JsonResponse
    {
        $command = RefundCreateCommand::from($request);

        $rid = $this->commandService->create($command);

        return static::success([ 'rid' => $rid ]);
    }

    public function show(Request $request, int $id) : OrderRefundResource
    {
        $refund = $this->queryService->find($id);

        return OrderRefundResource::make($refund);
    }

    public function destroy($id)
    {
    }
}
