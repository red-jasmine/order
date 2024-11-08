<?php

namespace RedJasmine\Order\UI\Http\Seller\Api\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RedJasmine\Order\Application\Services\OrderCommandService;
use RedJasmine\Order\Application\Services\RefundCommandService;
use RedJasmine\Order\Application\Services\RefundQueryService;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeRefundCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundAgreeReturnGoodsCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCancelCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundConfirmCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundCreateCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundRejectCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundLogisticsReshipmentCommand;
use RedJasmine\Order\Application\UserCases\Commands\Refund\RefundReturnGoodsCommand;
use RedJasmine\Order\UI\Http\Seller\Api\Resources\OrderRefundResource;
use RedJasmine\Support\Domain\Data\Queries\FindQuery;
use RedJasmine\Support\Domain\Data\Queries\PaginateQuery;

class RefundController extends Controller
{

    public function __construct(
        protected readonly RefundQueryService $queryService,
        protected RefundCommandService        $commandService,
        protected OrderCommandService         $orderCommandService,
    )
    {

        $this->queryService->getRepository()->withQuery(function ($query) {
            $query->onlySeller($this->getOwner());
        });


    }

    public function index(Request $request) : AnonymousResourceCollection
    {
        $result = $this->queryService->paginate(PaginateQuery::from($request->query()));
        return OrderRefundResource::collection($result);
    }


    public function show(Request $request, int $id) : OrderRefundResource
    {
        $refund = $this->queryService->findById(FindQuery::make($id,$request));

        return OrderRefundResource::make($refund);
    }

    public function store(Request $request) : JsonResponse
    {
        $command = RefundCreateCommand::from($request);

        $refundId = $this->commandService->create($command);

        return static::success([ 'id' => $refundId ]);
    }


    public function reject(Request $request) : JsonResponse
    {
        $command = RefundRejectCommand::from($request);
        $this->queryService->findById(FindQuery::make($command->id));
        $this->commandService->reject($command);

        return static::success();

    }

    public function cancel(Request $request) : JsonResponse
    {
        $command = RefundCancelCommand::from($request);
        $this->queryService->findById(FindQuery::make($command->id));
        $this->commandService->cancel($command);

        return static::success();

    }

    public function refundGoods(Request $request) : JsonResponse
    {
        $command = RefundReturnGoodsCommand::from($request);

        $this->queryService->findById(FindQuery::make($command->id));
        $this->commandService->returnGoods($command);
        return static::success();
    }


    public function agreeRefund(Request $request) : JsonResponse
    {
        $command = RefundAgreeRefundCommand::from($request->all());
        $this->queryService->findById(FindQuery::make($command->id));

        $this->commandService->agreeRefund($command);

        return static::success();
    }

    public function agreeReturnGoods(Request $request) : JsonResponse
    {
        $command = RefundAgreeReturnGoodsCommand::from($request->all());
        $this->queryService->findById(FindQuery::make($command->id));

        $this->commandService->agreeReturnGoods($command);

        return static::success();
    }


    public function confirm(Request $request) : JsonResponse
    {
        $command = RefundConfirmCommand::from($request->all());
        $this->queryService->findById(FindQuery::make($command->id));

        $this->commandService->confirm($command);

        return static::success();
    }


    // TODO 改造
    public function reshipment(Request $request) : JsonResponse
    {
        $command = RefundLogisticsReshipmentCommand::from($request->all());
        $this->queryService->findById(FindQuery::make($command->id));

        $this->commandService->reshipment($command);

        return static::success();
    }


    public function destroy($id) : void
    {
        abort(405);
    }
}
