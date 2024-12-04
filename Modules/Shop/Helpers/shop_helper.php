<?php

use Modules\Shop\Entities\Category;

if (!function_exists('shop_product_link')){
    function shop_product_link($product){
        $categorySlug = 'produk';

        if ($product->categories->count() > 0){
            $categorySlug =$product->categories-> first()->slug;
        }

        // return url($categorySlug. '/'. $product->slug.'-'. $product->sku);  jika menggunakann route

        $productSlug = $product->slug.'-' .$product->sku;
        return route('products.show', [$categorySlug,$productSlug]);
    }
}

if (!function_exists('shop_category_link')){
    function shop_category_link ($category){
        return route('products.category', [$category->slug]);
    }
}