<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }
        return app('alipay')->web([
            'out_trade_no' => $order->no,
            'total_amount' => $order->total_amount,
            'subject' => '支付 Laravel Shop:' . $order->no,
        ]);
    }
    public function alipayReturn()
    {
        try {
            $data = app('alipay')->verify();
            // if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            //     return app('alipay')->success();
            // }
            $order = Order::where('no', $data->out_trade_no)->first();
            if (!$order) {
                return 'fail';
            }
            // if ($order->paid_at) {
            //     return app('alipay')->success();
            // }
            $order->update([
                'paid_at' => Carbon::now(),
                'payment_method' => 'alipay',
                'payment_no' => $data->trade_no,
            ]);
            event(new OrderPaid($order));
            // return app('alipay')->success();
        } catch (Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }
        return view('pages.success', ['msg' => '付款成功']);
    }
    public function alipayNotify()
    {
        // 校验输入参数
        $data  = app('alipay')->verify();
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有交易状态：https://docs.open.alipay.com/59/103672
        if (!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if (!$order) {
            return 'fail';
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
        ]);
        event(new OrderPaid($order));

        return app('alipay')->success();
    }
    public function payByWechat(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }
        $wechatOrder = app('wechat_pay')->scan([
            'ort_trade_no' => $order->no,
            'total_fee' => $order->total_amount * 100,
            'body' => '支付laravel shop的订单:' . $order->no,
        ]);
        $qrCode = new QrCode($wechatOrder->code_url);
        return response($qrCode->writeString(), 200, ['Content-type' => $qrCode->getContentType()]);
    }
    public function wechatNotify()
    {
        $data = app('wechat_pay')->verify();
        $order = Order::where('no', $data->out_trade_no)->first();
        if (!$order) {
            return 'fail';
        }
        $order->update([
            'paid_at' => Carbon::now(),
            'payment_method' => 'wechat',
            'payment_no' => $data->transaction_id,
        ]);
        event(new OrderPaid($order));

        return app('wechat_pay')->success();
    }
}
