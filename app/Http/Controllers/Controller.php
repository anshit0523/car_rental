<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
abstract class Controller
{
    use AuthorizesRequests, ValidatesRequests, Dispatchable;
}
