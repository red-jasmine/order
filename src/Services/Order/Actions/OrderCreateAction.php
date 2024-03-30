<?php

namespace RedJasmine\Order\Services\Order\Actions;

use Illuminate\Database\Eloquent\Model;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderInfo;
use RedJasmine\Order\Services\Order\Data\OrderData;
use RedJasmine\Order\Services\Order\Pipelines\OrderCreatePipelines;
use RedJasmine\Order\Services\Order\Validators\OrderCreateValidator;
use RedJasmine\Support\Foundation\Service\Actions\CreateAction;

/**
 * @property Order $model
 */
class OrderCreateAction extends CreateAction
{
    protected ?string $dataClass = OrderData::class;

    protected function resolveModel() : void
    {
        if ($this->key) {
            $query       = $this->getModelClass()::query();
            $this->model = $this->service->callQueryCallbacks($query)
                                         ->findOrFail($this->key);
        } else {
            $this->model = app($this->getModelClass());
            $this->model->setRelation('info', new OrderInfo());
            $this->model->setRelation('products', collect());
        }
    }


    protected array $validatorCombiners = [
        OrderCreateValidator::class,
    ];

    protected function fill(array $data) : ?Model
    {
        return $this->model;
    }


    public function pipes() : array
    {
        return [
            OrderCreatePipelines::class
        ];
    }


    public function handle() : Model
    {
        $order               = $this->model;
        $order->id           = $this->service::buildID();
        $order->created_time = now();
        $order->products->each(function ($product) use ($order) {
            $product->id           = $product->id ?? $this->service::buildID();
            $product->order_id     = $order->id;
            $product->order_status = $order->order_status;
            $product->creator      = $order->creator;
            $product->created_time = $order->created_time;
        });
        $order->save();
        $order->info()->save($order->info);
        $order->products()->saveMany($order->products);
        if ($order->address) {
            // TODO 判断是否需要存储地址
            $order->address->id = $order->id;
            $order->address()->save($order->address);
        }
        $order->products->each(function ($product) {
            $product->info()->save($product->info);
        });
        return $order;

    }


}
