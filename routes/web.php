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

Auth::routes();

Route::get('/', 'HomeController@index');
Route::get('/admin', 'AdminController@index')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class]);

Route::get('/admin/organizer-events/{event}', 'OrganizerEventController@admin')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class]);
Route::get('organizer-events', 'OrganizerEventController@index');
Route::get('organizer-events/{event}', 'OrganizerEventController@show')->middleware(['auth']);
Route::post('organizer-events/{event}/registrations', 'OrganizerEventRegistrationController@store');
Route::post('organizer-events', 'OrganizerEventController@store')->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class]);
