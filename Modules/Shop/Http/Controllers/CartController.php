<?php

namespace Modules\Shop\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Modules\Shop\Repositories\front\interfaces\CartRepositoryInterfaces;
use Modules\shop\Repositories\front\interfaces\ProductRepositoryInterfaces;
use Modules\Shop\Entities\Product;
class CartController extends Controller
{

    protected $cartRepository;
    protected $productRepository;
    
    public function __construct(CartRepositoryInterfaces $cartRepository,
                                ProductRepositoryInterfaces $productRepository
    )
    {
        $this->cartRepository =$cartRepository;
        $this->productRepository= $productRepository;
    }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        // dd('test');

        // dd(auth()->user()->toArray());
        $cart=$this->cartRepository->findByUser(auth()->user());
        // dd($cart->toArray());
        $this-> data['cart']=$cart;
        return $this-> loadTheme('carts.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('shop::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $productID= $request->get('product_id');
        $qty= $request->get('qty');

        $product = $this->productRepository->findByID($productID);

        if ($product->stock_status != Product::STATUS_IN_STOCK) {
            return redirect(shop_product_link($product))->with('error', 'Tidak ada stok produk');
        }

        if ($product->stock < $qty) {
            return redirect(shop_product_link($product))->with('error', 'Stok produk tidak mencukupi');
        }

        $item =$this->cartRepository->addItem($product, $qty);

        if (!$item){
            return redirect(shop_product_link($product))->with('error', 'Tidak dapat menambahkan item ke Keranjang');

        }
            return redirect(shop_product_link($product))->with('success', 'Berhasil menambahkan item ke Keranjang');


        // dd($product->toArray()); 

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('shop::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('shop::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request)
    {
        $items = $request->get('qty');
        // dd($request->all);
        $this->cartRepository->updateQty($items);

        return redirect(route('carts.index'))->with('success', 'Keranjang telah diperbaharui');


    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $this->cartRepository->removeItem($id);    
    
        return redirect(route('carts.index'))->with('success', 'Berhasil menghapus item dari keranjang');

    }
}
