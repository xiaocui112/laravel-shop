<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\CouponCodeUnavailableException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
use App\Models\CouponCode;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        $coupon = null;
        if ($code = $request->input('coupon_code')) {
            $coupon = CouponCode::where('code', $code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('哟会卷不存在');
            }
        }
        return $orderService->store($user, $address, $request->remark, $request->items, $coupon);
    }
    public function index(Request $request)
    {
        $order = Order::query()->with(['items.product', 'items.productSku'])->where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->paginate();
        return view('orders.index', ['orders' => $order]);
    }
    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }
    public function received(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        if ($order->ship_status != Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);
        return $order;
    }
    public function review(Order $order)
    {
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付,不可评价');
        }
        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付,不可评价');
        }
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价,不可重复评价');
        }
        $reviews = $request->input('reviews');
        DB::transaction(function () use ($reviews, $order) {
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            $order->update(['reviewed' => true]);
        });
        event(new OrderReviewed($order));
        return redirect()->back();
    }
    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付,不可退款');
        }
        if ($order->refund_status != Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已经申请过退款,请勿重复申请');
        }
        $extra = $order->extra ?? [];
        $extra['refund_reason'] = $request->input('reason');
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra,
        ]);
        return $order;
    }
}
