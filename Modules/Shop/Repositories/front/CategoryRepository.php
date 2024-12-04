<?php

namespace Modules\Shop\Repositories\front;

use Modules\Shop\Entities\Category;
use Modules\Shop\Repositories\front\interfaces\CategoryRepositoryInterfaces;

class CategoryRepository implements CategoryRepositoryInterfaces {
    
    public function findAll($options = [])
    {
        return Category::orderBy('name', 'asc')->get();
    }

    public function findBySlug($slug)
    {
        return Category::where('slug', $slug)->firstOrFail();
    }
}