<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Responses\AppResponseFactory;

Route::get('/', function () {
    // return AppResponseFactory::make()
    return "Hello World";
});
