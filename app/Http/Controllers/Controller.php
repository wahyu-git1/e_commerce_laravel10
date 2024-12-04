<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected $data =[];
    protected $perPage =5;

    public function __construct()
    {
        
    }

    protected function loadTheme($view,$data=[])
        {
        return view ('themes/'. env('APP_THEME', 'default'). '/'.$view, $data);

    }

}
