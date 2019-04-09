<?php

use Illuminate\Http\Request;

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

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});
Route::group(['prefix' => 'reg'], function ($router) {
    Route::post('registration', 'RegController@registration');
});
Route::group(['middleware' => 'jwt.auth'], function ($router) {
    Route::get('customers', 'CustomersController@all');         //remove after complete
    Route::get('customers/{id}', 'CustomersController@get');    //remove after complete
    Route::post('customers/new', 'CustomersController@new');    //remove after complete
    Route::get('tasks', 'TasksController@get');                 //get all tasks for current user
    Route::post('tasks#', 'TasksController@newtask');            //add new task to project
    Route::post('tasks#', 'TasksController@projectupdate');      //update tasks order in project
    Route::post('tasks#', 'TasksController@tasktupdate');        //edit task
    Route::post('tasks#', 'TasksController@projectupdate');      //edit project
});