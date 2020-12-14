<?php

use Illuminate\Support\Facades\Route;
use App\Services\ApiClientService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (ApiClientService $service) {
    
    return view('products', [ 'products' => $service->getProducts() ]);
});
