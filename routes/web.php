<?php

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

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::middleware(['auth'])->group(function () {

    //client
    Route::get('/clientes', 'ClientsController@index');
    Route::get('/clientes/novo', 'ClientsController@form');
    Route::post('/clientes/insert', 'ClientsController@insert');
    Route::get('/clientes/{cliente}/editar', 'ClientsController@edit');
    Route::patch('/clientes/{cliente}', 'ClientsController@update');
    Route::delete('/clientes/{cliente}', 'ClientsController@delete');

    //operations
    Route::get('/home', 'OperationController@index');
    Route::post('/operation/insert', 'OperationController@insert');
    Route::get('/operation/{cliente}/historico', 'OperationController@history');
    Route::get('/operation/pendencias', 'OperationController@pendencias');
    Route::get('/operation/{cliente}/print', 'OperationController@printHistory');
    Route::get('/operation/test-print', 'OperationController@printTest');

});









