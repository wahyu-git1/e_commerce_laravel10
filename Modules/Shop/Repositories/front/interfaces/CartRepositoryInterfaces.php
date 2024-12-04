<?php

namespace Modules\Shop\Repositories\front\interfaces;

use App\Models\User;
use Modules\Shop\Entities\Cart;
use Modules\Shop\Entities\CartItem;
use Modules\Shop\Entities\Product;



// catatan
/*
semua kelas disini adalah implementasi from cart repository.php
kemudian akan diambil atau dilempar atau digunakan 
*/



interface CartRepositoryInterfaces
{
    public function findByUser(User $user):Cart;
    public function addItem(Product $product, $qty):CartItem;
    public function removeItem($id): bool;
    public function updateQty($items = [ ]): void;
    public function clear(User $user):  void;

}
