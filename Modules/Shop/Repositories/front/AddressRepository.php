<?php

namespace Modules\Shop\Repositories\front;

use App\Models\User;
use Modules\Shop\Entities\Address;
use Modules\Shop\Repositories\front\interfaces\AddressRepositoryInterfaces;

class AddressRepository implements AddressRepositoryInterfaces {
    
    public function findByUser(User $user)
    {
        return Address::where('user_id', $user->id)->get();
    }

    public function findByID(string $id)
    {
        return address::findOrFail($id);
    }
}