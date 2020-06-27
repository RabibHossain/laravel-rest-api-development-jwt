<?php

use App\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/person/{person}', function(Person $person) {
    return $person;
});


$router->post('register', 'Api\Auth\AuthController@register');
$router->post('login', 'Api\Auth\AuthController@login');

$router->group(['middleware' => 'auth.jwt'], function() use ($router) {

    Route::post('refresh', 'Api\Auth\AuthController@refresh');
    Route::post('user', 'Api\Auth\AuthController@getAuthUser');
    Route::post('logout', 'Api\Auth\AuthController@logout');
    Route::post('addTask', 'TaskController@store');
    Route::get('allTasks', 'TaskController@index');
    Route::get('task/{id}', 'TaskController@show');
    Route::put('task/{id}', 'TaskController@update');
    Route::delete('deleteTask/{id}', 'TaskController@destroy');

});
