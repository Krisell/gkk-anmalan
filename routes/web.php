<?php

use App\Http\Controllers\SignAgreementsController;
use App\Http\Middleware\EnsureAgreementsAreSignedMiddleware;

Auth::routes();

Route::get('/', 'HomeController@index')->middleware(EnsureAgreementsAreSignedMiddleware::class);
Route::get('/sign-agreements', [SignAgreementsController::class, 'index'])->middleware('auth');
Route::post('/sign-agreements', [SignAgreementsController::class, 'store'])->middleware('auth');
Route::view('/inactivated', 'inactivated')->name('inactivated');

Route::group(['prefix' => 'records'], function () {
    Route::get('/', [\App\Http\Controllers\RecordsController::class, 'index']);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('google', [\App\Http\Controllers\AuthController::class, 'google']);
    Route::post('microsoft', [\App\Http\Controllers\AuthController::class, 'microsoft']);
});

Route::group(['middleware' => ['auth']], function () {
    Route::group(['prefix' => 'admin', 'middleware' => ['superadmin']], function () {
        Route::post('/accounts/promote/{user}', [\App\Http\Controllers\AccountController::class, 'promote']);
        Route::post('/accounts/demote/{user}', [\App\Http\Controllers\AccountController::class, 'demote']);

        Route::group(['prefix' => 'dev'], function () {
            Route::get('/phpinfo', [\App\Http\Controllers\DevController::class, 'phpinfo']);
            Route::get('/opcache', [\App\Http\Controllers\DevController::class, 'opcache']);
        });
    });

    Route::group(['middleware' => \App\Http\Middleware\GrantedUserMiddleware::class], function () {
        Route::group(['prefix' => 'documents'], function () {
            Route::get('/', [\App\Http\Controllers\DocumentController::class, 'index'])->middleware(EnsureAgreementsAreSignedMiddleware::class);
        });

        Route::group(['prefix' => 'events'], function () {
            Route::get('/', [\App\Http\Controllers\EventController::class, 'index'])->middleware(EnsureAgreementsAreSignedMiddleware::class);
            Route::get('{event}', [\App\Http\Controllers\EventController::class, 'show']);
            Route::post('{event}/registrations', [\App\Http\Controllers\EventRegistrationController::class, 'store']);
            Route::post('{event}/registrations/{registration}', [\App\Http\Controllers\EventRegistrationController::class, 'update']);
        });

        Route::group(['prefix' => 'competitions'], function () {
            Route::get('/', [\App\Http\Controllers\CompetitionController::class, 'index'])->middleware(EnsureAgreementsAreSignedMiddleware::class);
            Route::get('{competition}', [\App\Http\Controllers\CompetitionController::class, 'show']);
            Route::post('{competition}/registrations', [\App\Http\Controllers\CompetitionRegistrationController::class, 'store']);
            Route::post('{competition}/registrations/{registration}', [\App\Http\Controllers\CompetitionRegistrationController::class, 'update']);
        });
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'index']);
        Route::post('/name', [\App\Http\Controllers\ProfileController::class, 'updateName']);
        Route::post('/email', [\App\Http\Controllers\ProfileController::class, 'updateEmail']);
        Route::post('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword']);
    });
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::post('/accounts/inactivate/{user}', [\App\Http\Controllers\AccountController::class, 'inactivate']);
    Route::post('/accounts/reactivate/{user}', [\App\Http\Controllers\AccountController::class, 'reactivate']);

    Route::group(['prefix' => 'events'], function () {
        Route::get('/{event}', [\App\Http\Controllers\EventController::class, 'admin']);
        Route::get('/', [\App\Http\Controllers\EventController::class, 'adminIndex']);
        Route::post('/', [\App\Http\Controllers\EventController::class, 'store']);
        Route::patch('/{event}', [\App\Http\Controllers\EventController::class, 'update']);
        Route::delete('/{event}', [\App\Http\Controllers\EventController::class, 'destroy']);
    });

    Route::group(['prefix' => 'competitions'], function () {
        Route::get('/{competition}', [\App\Http\Controllers\CompetitionController::class, 'admin']);
        Route::get('/', [\App\Http\Controllers\CompetitionController::class, 'adminIndex']);
        Route::post('/', [\App\Http\Controllers\CompetitionController::class, 'store']);
        Route::patch('/{competition}', [\App\Http\Controllers\CompetitionController::class, 'update']);
        Route::delete('/{competition}', [\App\Http\Controllers\CompetitionController::class, 'destroy']);
    });

    Route::group(['prefix' => 'accounts'], function () {
        Route::get('', [\App\Http\Controllers\AccountController::class, 'index']);
        Route::post('/{user}/grant', [\App\Http\Controllers\AccountController::class, 'grant']);
        Route::delete('/{user}/grant', [\App\Http\Controllers\AccountController::class, 'destroyUngranted']);
    });

    Route::group(['prefix' => 'news'], function () {
        Route::get('/', [\App\Http\Controllers\NewsController::class, 'index']);
        Route::get('/{news}', [\App\Http\Controllers\NewsController::class, 'edit']);
        Route::post('/', [\App\Http\Controllers\NewsController::class, 'store']);
        Route::post('/{news}', [\App\Http\Controllers\NewsController::class, 'update']);
        Route::delete('/{news}', [\App\Http\Controllers\NewsController::class, 'destroy']);

        Route::group(['prefix' => 'email'], function () {
            Route::post('/preview', [\App\Http\Controllers\NewsEmailController::class, 'preview']);
            Route::post('/test', [\App\Http\Controllers\NewsEmailController::class, 'test']);
            Route::get('/{item}', [\App\Http\Controllers\NewsEmailController::class, 'show']);
        });
    });

    Route::group(['prefix' => 'documents'], function () {
        Route::get('/', [\App\Http\Controllers\DocumentsAdministratorController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\DocumentsAdministratorController::class, 'store']);
        Route::post('/{document}', [\App\Http\Controllers\DocumentsAdministratorController::class, 'update']);
        Route::delete('/{document}', [\App\Http\Controllers\DocumentsAdministratorController::class, 'destroy']);
    });

    Route::group(['prefix' => 'document-folders'], function () {
        Route::post('/', [\App\Http\Controllers\DocumentFolderController::class, 'store']);
        Route::post('/{folder}', [\App\Http\Controllers\DocumentFolderController::class, 'update']);
        Route::delete('/{folder}', [\App\Http\Controllers\DocumentFolderController::class, 'destroy']);
    });

    Route::group(['prefix' => 'results'], function () {
        Route::get('/', [\App\Http\Controllers\ResultsController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\ResultsController::class, 'store']);
        Route::delete('/{result}', [\App\Http\Controllers\ResultsController::class, 'destroy']);
    });
});
