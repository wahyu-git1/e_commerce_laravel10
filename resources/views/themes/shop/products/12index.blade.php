<!-- resources/views/products/index.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('products.create') }}" class="btn btn-primary">Add Product</a>
    <div class="row mt-4">
        @foreach ($products as $product)
            <div class="col-lg-3 col-6 mt-3 mt-lg-0">
                <div class="card card-product card-body p-lg-4 p3">
                    <a href="{{ route('products.edit', $product->id) }}">
                        <img src="https://placehold.co/600x800" alt="" class="img-fluid">
                    </a>
                    <h3 class="product-name mt-3">{{ $product->name }}</h3>
                    <div class="rating">
                        <i class="bx bxs-star"></i>
                        <i class="bx bxs-star"></i>
                        <i class="bx bxs-star"></i>
                        <i class="bx bxs-star"></i>
                        <i class="bx bxs-star"></i>
                    </div>
                    <div class="detail d-flex justify-content-between align-items-center mt-4">
                        <p class="price">IDR {{ $product->price }}</p>
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-cart"><i class="bx bx-cart-alt"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
