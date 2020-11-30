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



// Route::get('/', function () { return view('welcome'); });

Auth::routes(['register' => false]);

Route::get('/', 'HomeController@index')->name('home');

Route::get('company-detailed/{company}','HomeController@companyDetailed');
Route::get('employee-detailed/{employee}','HomeController@employeeDetailed');



Route::group(['prefix'=>'admin'],function(){
    
    Route::get('/', 'HomeController@admin')->name('admin');
    Route::get('/{all?}', "HomeController@admin")->name('admin')->where(['all'=>'.*']);
});

Route::get('logout' , '\App\Http\Controllers\Auth\LoginController@logout');

