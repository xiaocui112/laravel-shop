<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected CartService $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function add(AddCartRequest $request)
    {
        $skuId = $request->sku_id;
        $amount = $request->amount;
        $this->cartService->add($skuId, $amount);
        return [];
    }
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();
        $cartItems = $this->cartService->get();
        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }
    public function remove(ProductSku $sku, Request $request)
    {
        $this->cartService->remove($sku->id);
        return [];
    }
}
