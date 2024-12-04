<?php

namespace Modules\Shop\Repositories\front\interfaces;
use App\Models\User;

interface AddressRepositoryInterfaces
{
    public function findByUser(User $user);
    public function findByID(string $id);
}