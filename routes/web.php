<?php

Auth::routes();

Route::get('/', 'HomeController@index');

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
  Route::get('/', 'AdminController@index');
  Route::get('events/{event}', 'EventController@admin');
  Route::post('events', 'EventController@store');
});

Route::group(['prefix' => 'events', 'middleware' => 'auth'], function () {
  Route::get('/', 'EventController@index');
  Route::get('{event}', 'EventController@show');

  Route::post('{event}/registrations', 'EventRegistrationController@store');
});
