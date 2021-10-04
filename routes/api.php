<?php

use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Route;
use Zttp\Zttp;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/weather', function (){

    $apiKey = config('services.darksky.key');
    $lat = request('lat');
    $lon = request('lon');

    $response = Zttp::get("https://api.openweathermap.org/data/2.5/onecall?lat=$lat&lon=$lon&units=metric&appid=$apiKey");
    return $response->json();
});

Route::get('/city', function (){

    $apiKey = config('services.here.key');
    $cityName = request('cityName');
    $limitResults = 1;

    $response = Zttp::get("https://discover.search.hereapi.com/v1/discover?at=40.7307999,-73.9973085&limit=$limitResults&q=$cityName&apiKey=$apiKey");
    return $response->json();
});
