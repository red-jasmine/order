<?php

namespace RedJasmine\Order\Http\Buyer\Controllers\Api;


use Illuminate\Http\Request;
use RedJasmine\Order\OrderService;

class OrderController extends Controller
{


    public function __construct(protected OrderService $service)
    {
    }


    public function service() : OrderService
    {
        $this->service->setOwner($this->getOwner())
                      ->setOperator($this->getUser());
        return $this->service;
    }


    public function index()
    {


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
    }


    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
