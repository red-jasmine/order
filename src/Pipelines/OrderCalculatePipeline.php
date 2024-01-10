<?php

namespace RedJasmine\Order\Pipelines;

use RedJasmine\Order\Models\Order;

class OrderCalculatePipeline
{
    public function handle(Order $order, \Closure $next)
    {
        $this->calculateProducts($order);
        $this->calculateOrder($order);
        return $next($order);
    }


    protected function calculateProducts(Order $order) : void
    {
        foreach ($order->products as $product) {
            // 商品金额
            $product->amount = bcmul($product->num, $product->price, 2);
            // 成本金额
            $product->cost_amount = bcmul($product->num, $product->price, 2);
            // 计算税费
            $product->tax_amount = bcadd($product->tax_amount, 0, 2);
            // 单品优惠
            $product->discount_amount = bcadd($product->discount_amount, 0, 2);
            // 付款金额
            $product->payment_amount = bcsub(bcadd($product->amount, $product->tax_amount, 2), $product->discount_amount, 2);
            // 分摊优惠
            $product->divide_discount_amount = bcadd(0, 0, 2);
            // 分摊后付款金额
            $product->divided_payment_amount = bcsub($product->payment_amount, $product->divide_discount_amount, 2);
        }
    }


    protected function calculateOrder(Order $order) : void
    {
        // 计算商品金额
        $order->total_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->payment_amount, 2);
        }, 0);
        // 统计成本
        $order->cost_amount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->cost_amount, 2);
        }, 0);
        // 邮费
        $order->freight_amount = bcadd(0, 0, 2);
        // 订单优惠
        $order->discount_amount = bcadd(0, 0, 2);
        // 计算付款 金额 = 商品总金额 + 邮费 - 优惠
        $order->payment_amount = bcsub(bcadd($order->total_amount, $order->freight_amount, 2), $order->discount_amount, 2);
        // TODO 计算分摊

    }

    /**
     * 计算分摊优惠
     * @return void
     */
    public function calculateDivideDiscount(Order $order)
    {
        $order->discount_amount;

        $totalAmount = $order->products->reduce(function ($sum, $product) {
            return bcadd($sum, $product->amount, 2);
        }, 0);
        // 对商品进行排序 从小到大
        $products = $order->products->sortBy('product_amount')->values();
    }
}
