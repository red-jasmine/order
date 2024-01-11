<?php

namespace RedJasmine\Order\Application\Http\Buyer\Controllers\Api;


use Illuminate\Http\Request;
use RedJasmine\Order\Business\Buyer\OrderService;
use RedJasmine\Order\Application\Http\Buyer\Resources\OrderResource;

class OrderController extends Controller
{


    public function __construct(protected OrderService $service)
    {
    }


    public function service() : OrderService
    {
        $this->service->setOwner($this->getOwner())->setOperator($this->getUser());
        return $this->service;
    }


    public function index()
    {

        $result = $this->service()->queries()->lists();
        return $this->success(OrderResource::collection($result));

    }

    public function store(Request $request)
    {
        $data    = $request->all();
        $creator = $this->service()->creator();

        $creator->setBuyer($this->getOwner());
        $creator->setSeller($this->getOwner());
        $creator->make($data);
        dd($creator);
    }

    public function show($id)
    {
        $order = $this->service()->queries()->find($id);
        return $this->success(new OrderResource($order));
    }


    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}