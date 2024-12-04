<?php

namespace Modules\Shop\Repositories\front\interfaces;

interface TagRepositoryInterfaces
{
    public function findAll($options = []);
    public function findBySlug($slug);
}