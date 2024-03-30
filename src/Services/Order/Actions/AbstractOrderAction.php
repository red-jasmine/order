<?php

namespace RedJasmine\Order\Services\Order\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RedJasmine\Order\Exceptions\OrderException;
use RedJasmine\Order\Models\Order;
use RedJasmine\Order\Models\OrderProduct;
use RedJasmine\Order\Services\Order\Enums\OrderStatusEnum;
use RedJasmine\Order\Services\Order\Enums\PaymentStatusEnum;
use RedJasmine\Order\Services\Order\Enums\ShippingStatusEnum;
use RedJasmine\Order\Services\Order\OrderService;
use RedJasmine\Support\Data\Data;
use RedJasmine\Support\Exceptions\AbstractException;
use RedJasmine\Support\Foundation\Service\Actions\ResourceAction;

/**
 * @property OrderService $service
 * @property Order        $model
 */
abstract class AbstractOrderAction extends ResourceAction
{


    /**
     * @param Order|OrderProduct $model
     *
     * @return bool
     * @throws OrderException
     */
    public function isAllowAction(Order|OrderProduct $model) : bool
    {
        $this->allowStatus($model);
        return true;
    }

    /**
     * 允许操作的订单状态
     * null 为所有都允许
     * @var array|null|OrderStatusEnum[]
     */
    protected ?array $allowOrderStatus = null;

    /**
     * 禁止操作的订单状态
     * @var array|null
     */
    protected ?array $forbidOrderStatus = null;

    /**
     * @var array|null|PaymentStatusEnum[]
     */
    protected ?array $allowPaymentStatus = null;

    protected ?array $forbidPaymentStatus = null;
    /**
     * @var array|null|ShippingStatusEnum[]
     */
    protected ?array $allowShippingStatus = null;

    protected ?array $forbidShippingStatus = null;

    /**
     * @param Order|OrderProduct $model
     *
     * @return bool
     * @throws OrderException
     */
    protected function allowStatus(Order|OrderProduct $model) : bool
    {
        $this->checkStatus($model->order_status, $this->allowOrderStatus, $this->forbidOrderStatus);
        $this->checkStatus($model->payment_status, $this->allowPaymentStatus, $this->forbidPaymentStatus);
        $this->checkStatus($model->shipping_status, $this->allowShippingStatus, $this->forbidShippingStatus);
        return true;
    }


    /**
     * @param            $status
     * @param array|null $allowStatus  允许的状态
     * @param array|null $forbidStatus 禁止的状态
     *
     * @return bool
     * @throws OrderException
     */
    protected function checkStatus($status, ?array $allowStatus, ?array $forbidStatus) : bool
    {
        if (($forbidStatus !== null) && in_array($status, $forbidStatus, true)) {
            throw new OrderException('当前状态禁止操作');
        }
        if ($allowStatus === null) {
            return true;
        }
        if (!in_array($status, $allowStatus, true)) {
            throw new OrderException($status->label() . ' 不支持操作');
        }
        return true;
    }


    /**
     * 是否需要锁
     * @var bool
     */
    protected bool $lockForUpdate = false;


    /**
     * @param Order|OrderProduct $model
     *
     * @return bool
     * @throws OrderException
     */
    protected function authorize(Order|OrderProduct $model) : bool
    {
        return $this->isAllowAction($model);

    }

    protected function fill(array $data) : ?Model
    {
        return $this->model;
    }


    protected function init($data) : Data
    {
        return $this->conversionData($data);
    }

    protected function validate() : array
    {
        if ($this->getValidator()) {
            $this->getValidator()->validate();
            return $this->getValidator()->safe()->all();
        }
        return $this->data->toArray();
    }

    // 数据持久化
    protected function handle() : mixed
    {
        return $this->model;
    }

    protected function after($handleResult) : mixed
    {
        return $handleResult;
    }


    protected bool $processHasData = true;

    /**
     * @return mixed
     * @throws AbstractException
     * @throws OrderException
     */
    protected function process() : mixed
    {
        try {
            DB::beginTransaction();
            $this->resolveModel();
            $this->getPipelines()->call('authorize', fn() => $this->authorize($this->model));
            // 数据处理
            if ($this->processHasData) {
                $this->data = $this->getPipelines()->call('init', fn() => $this->init($this->data));
                $this->makeValidator($this->data->toArray());
                $data = $this->getPipelines()->call('validate', fn() => $this->validate());
                $this->getPipelines()->call('fill', fn() => $this->fill($data));
            }
            $handleResult = $this->getPipelines()->call('handle', fn() => $this->handle());
            DB::commit();
        } catch (AbstractException $exception) {
            DB::rollBack();
            throw  $exception;
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw  $throwable;
        }
        return $this->getPipelines()->call('after', fn() => $this->after($handleResult));
    }


}
