<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Patrick\HttpStatusHelper;

class Controller extends HttpStatusHelper
{
    use AuthorizesRequests, ValidatesRequests;


}