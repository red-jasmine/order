<?php

namespace RedJasmine\Order\UI\Http\Buyer\Api\Controller;

use Illuminate\Http\Request;
use RedJasmine\Order\Application\Services\OrderCommandService;
use RedJasmine\Order\Application\Services\RefundCommandService;
use RedJasmine\Order\Application\Services\RefundQueryService;

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

    public function index(Request $request)
    {

    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function destroy($id)
    {
    }
}
