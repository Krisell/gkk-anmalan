<?php

use App\Http\Controllers\SignAgreementsController;
use App\Http\Middleware\EnsureAgreementsAreSignedMiddleware;

Auth::routes();

Route::get('/', 'HomeController@index')->middleware(EnsureAgreementsAreSignedMiddleware::class);
Route::get('/sign-agreements', [SignAgreementsController::class, 'index'])->middleware('auth');
Route::post('/sign-agreements', [SignAgreementsController::class, 'store'])->middleware('auth');

Route::view('/inactivated', 'inactivated')->name('inactivated');

Route::group(['prefix' => 'records'], function () {
    Route::get('/', 'RecordsController@index');
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('google', 'AuthController@google');
    Route::post('microsoft', 'AuthController@microsoft');
});

Route::group(['middleware' => ['auth']], function () {
    Route::group(['prefix' => 'admin', 'middleware' => ['superadmin']], function () {
        Route::post('/accounts/promote/{user}', 'AccountController@promote');
        Route::post('/accounts/demote/{user}', 'AccountController@demote');

        Route::group(['prefix' => 'dev'], function () {
            Route::get('/phpinfo', 'DevController@phpinfo');
            Route::get('/opcache', 'DevController@opcache');
        });
    });

    Route::group(['middleware' => \App\Http\Middleware\GrantedUserMiddleware::class], function () {
        Route::group(['prefix' => 'documents'], function () {
            Route::get('/', 'DocumentController@index')->middleware(EnsureAgreementsAreSignedMiddleware::class);
        });

        Route::group(['prefix' => 'events'], function () {
            Route::get('/', 'EventController@index')->middleware(EnsureAgreementsAreSignedMiddleware::class);
            Route::get('{event}', 'EventController@show');
            Route::post('{event}/registrations', 'EventRegistrationController@store');
            Route::post('{event}/registrations/{registration}', 'EventRegistrationController@update');
        });

        Route::group(['prefix' => 'competitions'], function () {
            Route::get('/', 'CompetitionController@index')->middleware(EnsureAgreementsAreSignedMiddleware::class);
            Route::get('{competition}', 'CompetitionController@show');
            Route::post('{competition}/registrations', 'CompetitionRegistrationController@store');
            Route::post('{competition}/registrations/{registration}', 'CompetitionRegistrationController@update');
        });
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', 'ProfileController@index');
        Route::post('/name', 'ProfileController@updateName');
        Route::post('/email', 'ProfileController@updateEmail');
        Route::post('/password', 'ProfileController@updatePassword');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::post('/accounts/inactivate/{user}', 'AccountController@inactivate');
    Route::post('/accounts/reactivate/{user}', 'AccountController@reactivate');

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

    Route::group(['prefix' => 'accounts'], function () {
        Route::get('', 'AccountController@index');
        Route::post('/{user}/grant', 'AccountController@grant');
        Route::delete('/{user}/grant', 'AccountController@destroyUngranted');
    });

    Route::group(['prefix' => 'news'], function () {
        Route::get('/', 'NewsController@index');
        Route::get('/{news}', 'NewsController@edit');
        Route::post('/', 'NewsController@store');
        Route::post('/{news}', 'NewsController@update');
        Route::delete('/{news}', 'NewsController@destroy');
    });

    Route::group(['prefix' => 'documents'], function () {
        Route::get('/', 'DocumentsAdministratorController@index');
        Route::post('/', 'DocumentsAdministratorController@store');
        Route::post('/{document}', 'DocumentsAdministratorController@update');
        Route::delete('/{document}', 'DocumentsAdministratorController@destroy');
    });

    Route::group(['prefix' => 'document-folders'], function () {
        Route::post('/', [\App\Http\Controllers\DocumentFolderController::class, 'store']);
        Route::post('/{folder}', [\App\Http\Controllers\DocumentFolderController::class, 'update']);
        Route::delete('/{folder}', [\App\Http\Controllers\DocumentFolderController::class, 'destroy']);
    });
});
