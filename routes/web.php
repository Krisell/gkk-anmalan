<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminCreateAccountsController;
use App\Http\Controllers\AdminPaymentsController;
use App\Http\Controllers\AdminPaymentToolsController;
use App\Http\Controllers\AdminSlideshowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\CompetitionRegistrationController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentFolderController;
use App\Http\Controllers\DocumentsAdministratorController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\FortnoxIntegrationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImpersonationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LagSMController;
use App\Http\Controllers\LicenseAdminController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\NewsEmailController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RecordsController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\SignAgreementsController;
use App\Http\Controllers\SlideshowController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\EnsureAgreementsAreSignedMiddleware;
use App\Http\Middleware\GrantedUserMiddleware;
use App\Http\Middleware\SuperadminMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/!', [HomeController::class, 'exclamation']);
Route::get('/', [HomeController::class, 'index']);

Route::get('/dm', [HomeController::class, 'dm']);
Route::redirect('/musik', '/musikhjalpen');
Route::get('/dm2025', [HomeController::class, 'dm']);
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

Route::prefix('api')->middleware(['auth', EnsureAgreementsAreSignedMiddleware::class])->group(function () {
    Route::get('/ranking/points', [RankingController::class, 'getPointsRanking']);
});

Route::get('/sign-agreements', [SignAgreementsController::class, 'index'])->middleware('auth');
Route::post('/sign-agreements/{agreement}', [SignAgreementsController::class, 'store'])->middleware('auth');
Route::view('/inactivated', 'inactivated')->name('inactivated');

Route::prefix('klubbrekord')->group(function () {
    Route::get('/', [RecordsController::class, 'index']);
});

Route::prefix('auth')->group(function () {
    Route::post('google', [AuthController::class, 'google']);
    Route::post('microsoft', [AuthController::class, 'microsoft']);
});

Route::middleware('auth')->group(function () {
    Route::delete('/impersonate', [ImpersonationController::class, 'delete']);

    Route::prefix('admin')->middleware(SuperadminMiddleware::class)->group(function () {
        Route::post('/accounts/promote/{user}', [AccountController::class, 'promote']);
        Route::post('/accounts/demote/{user}', [AccountController::class, 'demote']);
        Route::post('/impersonate/{userOrId}', [ImpersonationController::class, 'store']);

        Route::prefix('dev')->group(function () {
            Route::get('/phpinfo', [DevController::class, 'phpinfo']);
            Route::get('/opcache', [DevController::class, 'opcache']);
        });
    });

    Route::middleware(GrantedUserMiddleware::class)->group(function () {
        Route::get('/points', [RankingController::class, 'index']);

        Route::prefix('member-documents')->group(function () {
            Route::get('/', [DocumentController::class, 'index'])->middleware(EnsureAgreementsAreSignedMiddleware::class);
        });

        Route::prefix('events')->group(function () {
            Route::get('/', [EventController::class, 'index'])->middleware(EnsureAgreementsAreSignedMiddleware::class);
            Route::get('{event}', [EventController::class, 'show']);
            Route::post('{event}/registrations', [EventRegistrationController::class, 'store']);
            Route::post('{event}/registrations/{registration}', [EventRegistrationController::class, 'update']);
        });

        Route::prefix('competitions')->group(function () {
            Route::get('/', [CompetitionController::class, 'index'])->middleware(EnsureAgreementsAreSignedMiddleware::class);
            Route::get('{competition}', [CompetitionController::class, 'show']);
            Route::post('{competition}/registrations', [CompetitionRegistrationController::class, 'store']);
            Route::post('{competition}/registrations/{registration}', [CompetitionRegistrationController::class, 'update']);
        });
    });

    Route::prefix('profile')->middleware(EnsureAgreementsAreSignedMiddleware::class)->group(function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::post('/name', [ProfileController::class, 'updateName']);
        Route::post('/email', [ProfileController::class, 'updateEmail']);
        Route::post('/password', [ProfileController::class, 'updatePassword']);
    });

    Route::patch('/payments/{payment}', [PaymentController::class, 'update']);
});

Route::prefix('admin')->middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::prefix('accounts')->group(function () {
        Route::post('/inactivate/{user}', [AccountController::class, 'inactivate']);
        Route::post('/reactivate/{user}', [AccountController::class, 'reactivate']);
        Route::post('/', [AdminCreateAccountsController::class, 'store']);
    });

    Route::prefix('licenses/{user}/{year}')->group(function () {
        Route::post('/', [LicenseAdminController::class, 'store']);
    });

    Route::prefix('events')->controller(EventController::class)->group(function () {
        Route::get('/{event}', 'admin');
        Route::get('/', 'adminIndex');
        Route::post('/', 'store');
        Route::patch('/{event}', 'update');
        Route::delete('/{event}', 'destroy');

        Route::post('/{event}/registrations/', 'add');
    });

    Route::prefix('competitions')->controller(CompetitionController::class)->group(function () {
        Route::get('/{competition}', 'admin');
        Route::get('/', 'adminIndex');
        Route::post('/', 'store');
        Route::patch('/{competition}', 'update');
        Route::delete('/{competition}', 'destroy');

        Route::post('/{competition}/registrations/', 'add');
    });

    Route::prefix('accounts')->group(function () {
        Route::get('', [AccountController::class, 'index']);
        Route::post('/{user}/grant', [AccountController::class, 'grant']);
        Route::delete('/{user}/grant', [AccountController::class, 'destroyUngranted']);
        Route::patch('/{user}/competition-permission', [AccountController::class, 'updateCompetitionPermission']);
        Route::patch('/{user}/ren-vinnare-education', [AccountController::class, 'updateRenVinnareEducation']);
        Route::patch('/{user}/background-check', [AccountController::class, 'updateBackgroundCheck']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/', [AdminPaymentsController::class, 'index']);
    });

    Route::prefix('slideshow')->controller(AdminSlideshowController::class)->group(function () {
        Route::get('/', 'index');
    });

    Route::prefix('news')->group(function () {
        Route::get('/', [NewsController::class, 'index']);
        Route::get('/{news}', [NewsController::class, 'edit']);
        Route::post('/', [NewsController::class, 'store']);
        Route::post('/{news}', [NewsController::class, 'update']);
        Route::delete('/{news}', [NewsController::class, 'destroy']);

        Route::prefix('email')->group(function () {
            Route::post('/preview', [NewsEmailController::class, 'preview']);
            Route::post('/test', [NewsEmailController::class, 'test']);
            Route::get('/{item}', [NewsEmailController::class, 'show']);
        });
    });

    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentsAdministratorController::class, 'index']);
        Route::post('/', [DocumentsAdministratorController::class, 'store']);
        Route::post('/{document}', [DocumentsAdministratorController::class, 'update']);
        Route::delete('/{document}', [DocumentsAdministratorController::class, 'destroy']);
    });

    Route::prefix('document-folders')->group(function () {
        Route::post('/', [DocumentFolderController::class, 'store']);
        Route::post('/{folder}', [DocumentFolderController::class, 'update']);
        Route::delete('/{folder}', [DocumentFolderController::class, 'destroy']);
    });

    Route::prefix('results')->group(function () {
        Route::get('/', [ResultsController::class, 'index']);
        Route::post('/', [ResultsController::class, 'store']);
        Route::delete('/{result}', [ResultsController::class, 'destroy']);
    });

    Route::prefix('payment-tools')->controller(AdminPaymentToolsController::class)->group(function () {
        Route::get('/', 'index');
        Route::post('/generate-memberships/preview', 'previewGenerateMemberships');
        Route::post('/generate-memberships/execute', 'executeGenerateMemberships');
        Route::post('/generate-licenses/preview', 'previewGenerateLicenses');
        Route::post('/generate-licenses/execute', 'executeGenerateLicenses');
        Route::post('/competition-payments/preview', 'previewCreateCompetitionPayments');
        Route::post('/competition-payments/execute', 'executeCreateCompetitionPayments');
        Route::post('/sync-customers/preview', 'previewSyncCustomers');
        Route::post('/sync-customers/execute', 'executeSyncCustomers');
        Route::post('/create-invoices/preview', 'previewCreateInvoices');
        Route::post('/create-invoices/execute', 'executeCreateInvoices');
        Route::post('/email-invoices/preview', 'previewEmailInvoices');
        Route::post('/email-invoices/execute', 'executeEmailInvoices');
        Route::post('/verify-payments/preview', 'previewVerifyPayments');
        Route::post('/verify-payments/execute', 'executeVerifyPayments');
        Route::post('/remind-unpaid/preview', 'previewRemindUnpaidFees');
        Route::post('/remind-unpaid/execute', 'executeRemindUnpaidFees');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/invoices/{payment}', [InvoiceController::class, 'show']);
});

Route::middleware(['auth', SuperadminMiddleware::class])->prefix('fn')->group(function () {
    Route::get('/', [FortnoxIntegrationController::class, 'index'])->name('fortnox.index');
    Route::get('/invoices/pdf/{invoice}', [FortnoxIntegrationController::class, 'pdfInvoice']);
    Route::get('/invoices/email/{invoice?}', [FortnoxIntegrationController::class, 'emailInvoice']);
    Route::get('/invoices/{invoice?}', [FortnoxIntegrationController::class, 'invoices'])->name('fortnox.invoices');
    Route::get('/customers/{customer?}', [FortnoxIntegrationController::class, 'customers'])->name('fortnox.customers');
    Route::get('/articles/{article?}', [FortnoxIntegrationController::class, 'articles'])->name('fortnox.articles');
    Route::get('printtemplates', [FortnoxIntegrationController::class, 'printtemplates']);
    Route::get('activation', [FortnoxIntegrationController::class, 'activation'])->name('fortnox.activation');
    Route::get('disconnect', [FortnoxIntegrationController::class, 'disconnect'])->name('fortnox.disconnect');
});

Route::get('/webhooks/{action}', WebhookController::class)->name('webhook');

Route::view('/slides-for-screen', 'slides')->name('slides');

Route::prefix('slideshow')->controller(SlideshowController::class)->group(function () {
    Route::post('log', 'log');
    Route::get('slides', 'index');
    Route::post('order', 'updateOrder')->middleware(['auth', AdminMiddleware::class]);
    Route::patch('slides/{slide}', 'update')->middleware(['auth', AdminMiddleware::class]);
    Route::post('slides', 'store')->middleware(['auth', AdminMiddleware::class]);
    Route::put('slides/{slide}', 'updateSlide')->middleware(['auth', AdminMiddleware::class]);
    Route::delete('slides/{slide}', 'destroy')->middleware(['auth', AdminMiddleware::class]);
});
