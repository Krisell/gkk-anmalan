<?php

Auth::routes();

Route::get('/', 'HomeController@index');

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
  Route::get('events/{event}', 'EventController@admin');
  Route::get('events', 'AdminController@index');
  Route::post('events', 'EventController@store');
  Route::delete('events/{event}', 'EventController@destroy');
});

Route::group(['prefix' => 'events', 'middleware' => 'auth'], function () {
  Route::get('/', 'EventController@index');
  Route::get('{event}', 'EventController@show');

  Route::post('{event}/registrations', 'EventRegistrationController@store');
});

Route::group(['prefix' => 'auth'], function () {
  Route::post('google', 'AuthController@google');
  Route::post('microsoft', 'AuthController@microsoft');
});
