<?php

namespace Modules\shop\Repositories\front\interfaces;


use PhpOption\Option;

interface ProductRepositoryInterfaces{
    public function findAll($options= []);
    public function findBySKU($sku);
    public function findByID($id);
    
}