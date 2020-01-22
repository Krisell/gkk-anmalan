<?php

Auth::routes();

Route::get('/', 'HomeController@index');

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::group(['prefix' => 'events'], function () {
        Route::get('/{event}', 'EventController@admin');
        Route::get('/', 'EventController@adminIndex');
        Route::post('/', 'EventController@store');
        Route::patch('/{event}', 'EventController@update');
        Route::delete('/{event}', 'EventController@destroy');
    });

    Route::group(['prefix' => 'competitions'], function () {
        Route::get('/{competition}', 'CompetitionController@admin');
        Route::get('/', 'CompetitionController@adminIndex');
        Route::post('/', 'CompetitionController@store');
        Route::patch('/{competition}', 'CompetitionController@update');
        Route::delete('/{competition}', 'CompetitionController@destroy');
    });
});

Route::group(['prefix' => 'events', 'middleware' => 'auth'], function () {
    Route::get('/', 'EventController@index');
    Route::get('{event}', 'EventController@show');
    Route::post('{event}/registrations', 'EventRegistrationController@store');
});

Route::group(['prefix' => 'competitions', 'middleware' => 'auth'], function () {
    Route::get('/', 'CompetitionController@index');
    Route::get('{competition}', 'CompetitionController@show');
    Route::post('{competition}/registrations', 'CompetitionRegistrationController@store');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('google', 'AuthController@google');
    Route::post('microsoft', 'AuthController@microsoft');
});
