<?php

namespace Modules\Shop\Repositories\front\interfaces;

interface CategoryRepositoryInterfaces
{
    public function findAll($options = []);
    public function findBySlug($slug);
}