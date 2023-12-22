<?php

namespace RedJasmine\Order\Http\Buyer\Controllers\Api;


use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {




    }

    public function store(Request $request)
    {
        $data = $request->all();
        dd($data);
        dd(1);
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
