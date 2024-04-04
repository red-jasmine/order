<?php

namespace RedJasmine\Order\Domains\Order\Domain\Models;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use RedJasmine\Order\Domains\Common\Domain\Models\HasTradeParties;
use RedJasmine\Order\Domains\Order\Domain\Enums\OrderStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\OrderTypeEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\PaymentStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\RefundStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingStatusEnum;
use RedJasmine\Order\Domains\Order\Domain\Enums\ShippingTypeEnum;
use RedJasmine\Order\Domains\Order\Domain\Events\OrderCreatedEvent;
use RedJasmine\Support\Casts\AesEncrypted;
use RedJasmine\Support\Contracts\UserInterface;
use RedJasmine\Support\Data\UserData;
use RedJasmine\Support\Traits\HasDateTimeFormatter;
use RedJasmine\Support\Traits\Models\HasOperator;
use Spatie\LaravelData\WithData;

class Order extends Model
{

    use HasEvents;

    // TODO  has $events

    use WithData;

    use HasDateTimeFormatter;

    use SoftDeletes;

    use HasOperator;

    use HasTradeParties;

    public bool $withTradePartiesNickname = true;

    public $incrementing = false;


    protected $casts = [
        'order_type'       => OrderTypeEnum::class,
        'shipping_type'    => ShippingTypeEnum::class,
        'order_status'     => OrderStatusEnum::class,
        'payment_status'   => PaymentStatusEnum::class,
        'shipping_status'  => ShippingStatusEnum::class,
        'refund_status'    => RefundStatusEnum::class,
        'created_time'     => 'datetime',
        'payment_time'     => 'datetime',
        'close_time'       => 'datetime',
        'shipping_time'    => 'datetime',
        'collect_time'     => 'datetime',
        'dispatch_time'    => 'datetime',
        'signed_time'      => 'datetime',
        'end_time'         => 'datetime',
        'refund_time'      => 'datetime',
        'rate_time'        => 'datetime',
        'contact'          => AesEncrypted::class,
        'is_seller_delete' => 'boolean',
        'is_buyer_delete'  => 'boolean',
    ];


    public function info() : HasOne
    {
        return $this->hasOne(OrderInfo::class, 'id', 'id');
    }

    public function products() : HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function address() : HasOne
    {
        return $this->hasOne(OrderAddress::class, 'id', 'id');
    }

    public function logistics() : MorphMany
    {
        return $this->morphMany(OrderLogistics::class, 'shippable');
    }

    public function guide() : Attribute
    {
        return Attribute::make(
            get: static fn(mixed $value, array $attributes) => UserData::from([
                                                                                  'type' => $attributes['guide_type'],
                                                                                  'id'   => $attributes['guide_id'],
                                                                              ]),
            set: static fn(?UserInterface $user) => [
                'guide_type' => $user?->getType(),
                'guide_id'   => $user?->getID(),
            ]

        );
    }


    public function addProduct(OrderProduct $orderProduct) : static
    {
        $this->products->add($orderProduct);
        return $this;
    }


    public function calculateAmount() : static
    {
        // 统计商品金额
        $this->calculateProducts();
        // 汇总订单金额
        $this->calculateOrder();
        // 分摊订单数据
        $this->calculateDivideDiscount();
        return $this;
    }


    protected function calculateProducts() : void
    {
        foreach ($this->products as $product) {
            // 商品总金额   < 0 TODO 验证金额

            $product->product_amount = bcmul($product->num, $product->price, 2);
            // 成本金额
            $product->cost_amount = bcmul($product->num, $product->cost_price, 2);
            // 计算税费
            $product->tax_amount = bcadd($product->tax_amount, 0, 2);
            // 单品优惠
            $product->discount_amount = bcadd($product->discount_amount, 0, 2);
            // 应付金额  = 商品金额 + 税费 - 单品优惠

            $product->payable_amount = bcsub(bcadd($product->product_amount, $product->tax_amount, 2), $product->discount_amount, 2);

            // 实付金额 完成支付时
            $product->payment_amount = 0;

        }
    }


    protected function calculateOrder() : void
    {
        $order = $this;
        // 商品金额
        $order->total_product_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->product_amount, 2);
        }, 0);
        // 商品成本
        $order->total_cost_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->cost_amount, 2);
        }, 0);
        // 商品应付
        $order->total_payable_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->payable_amount, 2);
        }, 0);

        // 邮费
        $order->freight_amount = bcadd($order->freight_amount, 0, 2);
        // 订单优惠
        $order->discount_amount = bcadd($order->discount_amount, 0, 2);

        // 订单应付金额 = 商品总应付金额 + 邮费 - 优惠
        $order->payable_amount = bcsub(bcadd($order->total_payable_amount, $order->freight_amount, 2), $order->discount_amount, 2);

    }

    /**
     * 计算分摊优惠
     * @return void
     */
    public function calculateDivideDiscount()
    {
        $order = $this;
        $order->discount_amount;
        // 对商品进行排序 从小到大
        $products = $order->products->sortBy('product_amount')->values();
        // TODO
    }

    /**
     * @return $this|Model|Order
     * @throws Exception
     */
    public function create()
    {

        // 初始化订单状态
        // TODO 策略模式
        $this->order_status    = OrderStatusEnum::WAIT_BUYER_PAY;
        $this->payment_status  = null;
        $this->shipping_status = null;
        $this->refund_status   = null;
        $this->rate_status     = null;

        $this->created_time = now();
        $this->products->each(function (OrderProduct $orderProduct) {
            $orderProduct->order_id     = $this->id;
            $orderProduct->order_status = $this->order_status;
            $orderProduct->seller       = $this->seller;
            $orderProduct->buyer        = $this->buyer;
            $orderProduct->created_time = $this->created_time;
            $orderProduct->creator      = $this->creator;
        });
        if ($this->address) {
            $this->address->id = $this->id;
        }

        // 计算金额
        $this->calculateAmount();


        $this->addEvent((new OrderCreatedEvent(id: $this->id)));

        return $this;
    }


}
