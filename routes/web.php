<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LagSMController;
use App\Http\Controllers\SignAgreementsController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EnsureAgreementsAreSignedMiddleware;
use App\Http\Middleware\SuperadminMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/!', [HomeController::class, 'exclamation']);
Route::get('/', [HomeController::class, 'index']);

Route::get('/dm', [HomeController::class, 'dm']);
Route::redirect('/musik', '/musikhjalpen');
Route::get('/dm2022', [HomeController::class, 'dm']);
Route::get('/gm', [HomeController::class, 'gm']);
Route::get('/GM', [HomeController::class, 'gm']);

Route::get('/styrkelyft', [HomeController::class, 'powerlifting']);
Route::get('/gkk', [HomeController::class, 'about']);
Route::get('/medlem', [HomeController::class, 'member']);
Route::redirect('/member', '/medlem');
Route::get('/dokument', [HomeController::class, 'documents']);
Route::get('/prova-pa', [HomeController::class, 'provaPa']);

Route::controller(LagSMController::class)->group(function () {
    Route::get('/lag-sm', 'index');
    Route::get('/lagsm', 'index');
});

Route::prefix('insidan')->middleware(EnsureAgreementsAreSignedMiddleware::class)->group(function () {
    Route::get('/', [HomeController::class, 'inside']);
});

Route::get('/sign-agreements', [SignAgreementsController::class, 'index'])->middleware('auth');
Route::post('/sign-agreements/{agreement}', [SignAgreementsController::class, 'store'])->middleware('auth');
Route::view('/inactivated', 'inactivated')->name('inactivated');

Route::prefix('klubbrekord')->group(function () {
    Route::get('/', [\App\Http\Controllers\RecordsController::class, 'index']);
});

Route::prefix('auth')->group(function () {
    Route::post('google', [\App\Http\Controllers\AuthController::class, 'google']);
    Route::post('microsoft', [\App\Http\Controllers\AuthController::class, 'microsoft']);
});

Route::middleware('auth')->group(function () {
    Route::delete('/impersonate', [\App\Http\Controllers\ImpersonationController::class, 'delete']);

    Route::prefix('admin')->middleware(SuperadminMiddleware::class)->group(function () {
        Route::post('/accounts/promote/{user}', [\App\Http\Controllers\AccountController::class, 'promote']);
        Route::post('/accounts/demote/{user}', [\App\Http\Controllers\AccountController::class, 'demote']);
        Route::post('/impersonate/{userOrId}', [\App\Http\Controllers\ImpersonationController::class, 'store']);

        Route::prefix('dev')->group(function () {
            Route::get('/phpinfo', [\App\Http\Controllers\DevController::class, 'phpinfo']);
            Route::get('/opcache', [\App\Http\Controllers\DevController::class, 'opcache']);
        });
    });

    Route::middleware(\App\Http\Middleware\GrantedUserMiddleware::class)->group(function () {
        Route::prefix('member-documents')->group(function () {
            Route::get('/', [\App\Http\Controllers\DocumentController::class, 'index'])->middleware(EnsureAgreementsAreSignedMiddleware::class);
        });

        Route::prefix('events')->group(function () {
            Route::get('/', [\App\Http\Controllers\EventController::class, 'index'])->middleware(EnsureAgreementsAreSignedMiddleware::class);
            Route::get('{event}', [\App\Http\Controllers\EventController::class, 'show']);
            Route::post('{event}/registrations', [\App\Http\Controllers\EventRegistrationController::class, 'store']);
            Route::post('{event}/registrations/{registration}', [\App\Http\Controllers\EventRegistrationController::class, 'update']);
        });

        Route::prefix('competitions')->group(function () {
            Route::get('/', [\App\Http\Controllers\CompetitionController::class, 'index'])->middleware(EnsureAgreementsAreSignedMiddleware::class);
            Route::get('{competition}', [\App\Http\Controllers\CompetitionController::class, 'show']);
            Route::post('{competition}/registrations', [\App\Http\Controllers\CompetitionRegistrationController::class, 'store']);
            Route::post('{competition}/registrations/{registration}', [\App\Http\Controllers\CompetitionRegistrationController::class, 'update']);
        });
    });

    Route::prefix('profile')->middleware(EnsureAgreementsAreSignedMiddleware::class)->group(function () {
        Route::get('/', [\App\Http\Controllers\ProfileController::class, 'index']);
        Route::post('/name', [\App\Http\Controllers\ProfileController::class, 'updateName']);
        Route::post('/email', [\App\Http\Controllers\ProfileController::class, 'updateEmail']);
        Route::post('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword']);
    });

    Route::patch('/payments/{payment}', [\App\Http\Controllers\PaymentController::class, 'update']);
});

Route::prefix('admin')->middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::prefix('accounts')->group(function () {
        Route::post('/inactivate/{user}', [\App\Http\Controllers\AccountController::class, 'inactivate']);
        Route::post('/reactivate/{user}', [\App\Http\Controllers\AccountController::class, 'reactivate']);
        Route::post('/', [\App\Http\Controllers\AdminCreateAccountsController::class, 'store']);
    });

    Route::prefix('licenses/{user}/{year}')->group(function () {
        Route::post('/', [\App\Http\Controllers\LicenseAdminController::class, 'store']);
    });

    Route::prefix('events')->controller(EventController::class)->group(function () {
        Route::get('/{event}', 'admin');
        Route::get('/', 'adminIndex');
        Route::post('/', 'store');
        Route::patch('/{event}', 'update');
        Route::delete('/{event}', 'destroy');

        Route::post('/{event}/registrations/', 'add');
    });

    Route::prefix('competitions')->group(function () {
        Route::get('/{competition}', [\App\Http\Controllers\CompetitionController::class, 'admin']);
        Route::get('/', [\App\Http\Controllers\CompetitionController::class, 'adminIndex']);
        Route::post('/', [\App\Http\Controllers\CompetitionController::class, 'store']);
        Route::patch('/{competition}', [\App\Http\Controllers\CompetitionController::class, 'update']);
        Route::delete('/{competition}', [\App\Http\Controllers\CompetitionController::class, 'destroy']);
    });

    Route::prefix('accounts')->group(function () {
        Route::get('', [\App\Http\Controllers\AccountController::class, 'index']);
        Route::post('/{user}/grant', [\App\Http\Controllers\AccountController::class, 'grant']);
        Route::delete('/{user}/grant', [\App\Http\Controllers\AccountController::class, 'destroyUngranted']);
    });

    Route::prefix('news')->group(function () {
        Route::get('/', [\App\Http\Controllers\NewsController::class, 'index']);
        Route::get('/{news}', [\App\Http\Controllers\NewsController::class, 'edit']);
        Route::post('/', [\App\Http\Controllers\NewsController::class, 'store']);
        Route::post('/{news}', [\App\Http\Controllers\NewsController::class, 'update']);
        Route::delete('/{news}', [\App\Http\Controllers\NewsController::class, 'destroy']);

        Route::prefix('email')->group(function () {
            Route::post('/preview', [\App\Http\Controllers\NewsEmailController::class, 'preview']);
            Route::post('/test', [\App\Http\Controllers\NewsEmailController::class, 'test']);
            Route::get('/{item}', [\App\Http\Controllers\NewsEmailController::class, 'show']);
        });
    });

    Route::prefix('documents')->group(function () {
        Route::get('/', [\App\Http\Controllers\DocumentsAdministratorController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\DocumentsAdministratorController::class, 'store']);
        Route::post('/{document}', [\App\Http\Controllers\DocumentsAdministratorController::class, 'update']);
        Route::delete('/{document}', [\App\Http\Controllers\DocumentsAdministratorController::class, 'destroy']);
    });

    Route::prefix('document-folders')->group(function () {
        Route::post('/', [\App\Http\Controllers\DocumentFolderController::class, 'store']);
        Route::post('/{folder}', [\App\Http\Controllers\DocumentFolderController::class, 'update']);
        Route::delete('/{folder}', [\App\Http\Controllers\DocumentFolderController::class, 'destroy']);
    });

    Route::prefix('results')->group(function () {
        Route::get('/', [\App\Http\Controllers\ResultsController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\ResultsController::class, 'store']);
        Route::delete('/{result}', [\App\Http\Controllers\ResultsController::class, 'destroy']);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/invoices/{payment}', [\App\Http\Controllers\InvoiceController::class, 'show']);
});

Route::middleware(['auth', SuperadminMiddleware::class])->prefix('fn')->group(function () {
    Route::get('/', [\App\Http\Controllers\FortnoxIntegrationController::class, 'index'])->name('fortnox.index');
    Route::get('/invoices/pdf/{invoice}', [\App\Http\Controllers\FortnoxIntegrationController::class, 'pdfInvoice']);
    Route::get('/invoices/email/{invoice?}', [\App\Http\Controllers\FortnoxIntegrationController::class, 'emailInvoice']);
    Route::get('/invoices/{invoice?}', [\App\Http\Controllers\FortnoxIntegrationController::class, 'invoices'])->name('fortnox.invoices');
    Route::get('/customers/{customer?}', [\App\Http\Controllers\FortnoxIntegrationController::class, 'customers'])->name('fortnox.customers');
    Route::get('/articles/{article?}', [\App\Http\Controllers\FortnoxIntegrationController::class, 'articles'])->name('fortnox.articles');
    Route::get('printtemplates', [\App\Http\Controllers\FortnoxIntegrationController::class, 'printtemplates']);
    Route::get('activation', [\App\Http\Controllers\FortnoxIntegrationController::class, 'activation'])->name('fortnox.activation');
    Route::get('disconnect', [\App\Http\Controllers\FortnoxIntegrationController::class, 'disconnect'])->name('fortnox.disconnect');
});

Route::get('/webhooks/{action}', WebhookController::class)->name('webhook');

Route::view('/slides-for-screen', 'slides')->name('slides');
