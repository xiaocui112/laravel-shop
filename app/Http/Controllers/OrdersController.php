<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        return $orderService->store($user, $address, $request->remark, $request->items);
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
}
