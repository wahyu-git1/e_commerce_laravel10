<?php

namespace Modules\Shop\Repositories\front;

use Modules\Shop\Entities\Tag;
use Modules\Shop\Repositories\front\interfaces\TagRepositoryInterfaces;

class TagRepository implements TagRepositoryInterfaces {
    
    public function findAll($options = [])
    {
        return Tag::orderBy('name', 'asc')->get();
    }

    public function findBySlug($slug)
    {
        return Tag::where('slug', $slug)->firstOrFail();
    }
}