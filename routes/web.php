
<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventConceptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\RundownController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentCategoryController;
use App\Http\Controllers\EventEquipmentController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\EventVendorController;
use App\Http\Controllers\VendorConfirmationController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationInvitationController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    // ==========================================
    // MODUL GLOBAL (BELUM DIMIGRASIKAN)
    // ==========================================

    Route::middleware('role:Admin,Staf Operasional')->group(function () {

        // User Management
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');


        // Konsep Acara
        Route::get('/event-concepts', [EventConceptController::class, 'index'])
            ->name('event-concepts.index');

        Route::get('/event-concepts/create', [EventConceptController::class, 'create'])
            ->name('event-concepts.create');

        Route::post('/event-concepts', [EventConceptController::class, 'store'])
            ->name('event-concepts.store');

        Route::get('/event-concepts/{eventConcept}/edit', [EventConceptController::class, 'edit'])
            ->name('event-concepts.edit');

        Route::put('/event-concepts/{eventConcept}', [EventConceptController::class, 'update'])
            ->name('event-concepts.update');


        // Rundown
        Route::get('/rundowns', [RundownController::class, 'index'])
            ->name('rundowns.index');

        Route::get('/rundowns/create', [RundownController::class, 'create'])
            ->name('rundowns.create');

        Route::post('/rundowns', [RundownController::class, 'store'])
            ->name('rundowns.store');

        Route::get('/rundowns/{rundown}/edit', [RundownController::class, 'edit'])
            ->name('rundowns.edit');

        Route::put('/rundowns/{rundown}', [RundownController::class, 'update'])
            ->name('rundowns.update');

        Route::patch('/rundowns/{rundown}/publish', [RundownController::class, 'togglePublish'])
            ->name('rundowns.publish');


        // Vendors
        Route::get('/vendors', [VendorController::class, 'index'])
            ->name('vendors.index');

        Route::get('/vendors/create', [VendorController::class, 'create'])
            ->name('vendors.create');

        Route::post('/vendors', [VendorController::class, 'store'])
            ->name('vendors.store');

        Route::get('/vendors/{vendor}/edit', [VendorController::class, 'edit'])
            ->name('vendors.edit');

        Route::put('/vendors/{vendor}', [VendorController::class, 'update'])
            ->name('vendors.update');


        // Event Vendor
        Route::get('/event-vendors', [EventVendorController::class, 'index'])
            ->name('event-vendors.index');

        Route::get('/event-vendors/create', [EventVendorController::class, 'create'])
            ->name('event-vendors.create');

        Route::post('/event-vendors', [EventVendorController::class, 'store'])
            ->name('event-vendors.store');

        Route::get('/event-vendors/{eventVendor}/edit', [EventVendorController::class, 'edit'])
            ->name('event-vendors.edit');

        Route::put('/event-vendors/{eventVendor}', [EventVendorController::class, 'update'])
            ->name('event-vendors.update');


        // Evaluasi
        Route::get('/evaluations', [EvaluationController::class, 'index'])
            ->name('evaluations.index');

        Route::get('/evaluations/{event}/create', [EvaluationController::class, 'create'])
            ->name('evaluations.create');

        Route::post('/evaluations/{event}', [EvaluationController::class, 'store'])
            ->name('evaluations.store');

        Route::get('/evaluations/{evaluation}/edit', [EvaluationController::class, 'edit'])
            ->name('evaluations.edit');

        Route::put('/evaluations/{evaluation}', [EvaluationController::class, 'update'])
            ->name('evaluations.update');
    });


    // ==========================================
    // KONFIRMASI VENDOR (GLOBAL, BELUM DIMIGRASIKAN)
    // ==========================================

    Route::middleware('role:Admin,Staf Operasional,Vendor')->group(function () {

        Route::get('/vendor-confirmations', [VendorConfirmationController::class, 'index'])
            ->name('vendor-confirmations.index');

        Route::get('/vendor-confirmations/create', [VendorConfirmationController::class, 'create'])
            ->name('vendor-confirmations.create');

        Route::post('/vendor-confirmations', [VendorConfirmationController::class, 'store'])
            ->name('vendor-confirmations.store');

        Route::get('/vendor-confirmations/{vendorConfirmation}/edit', [VendorConfirmationController::class, 'edit'])
            ->name('vendor-confirmations.edit');

        Route::put('/vendor-confirmations/{vendorConfirmation}', [VendorConfirmationController::class, 'update'])
            ->name('vendor-confirmations.update');
    });


    // ==========================================
    // LAPORAN
    // ==========================================

    Route::middleware('role:Admin')->group(function () {

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('reports.index');
    });


    // ==========================================
    // ORGANISASI
    // ==========================================

    Route::get('/organizations', [OrganizationController::class, 'index'])
        ->name('organizations.index');

    Route::get('/organizations/create', [OrganizationController::class, 'create'])
        ->name('organizations.create');

    Route::post('/organizations', [OrganizationController::class, 'store'])
        ->name('organizations.store');


    // Semua fitur di bawah ini dibatasi berdasarkan organisasi.
    Route::prefix('/o/{organization:slug}')
        ->middleware('organization.member')
        ->scopeBindings()
        ->group(function () {

            // Workspace organisasi
            Route::get('/', [OrganizationController::class, 'workspace'])
                ->name('organizations.workspace');


            // ==========================================
            // FITUR ADMIN DAN STAF OPERASIONAL ORGANISASI
            // ==========================================

            Route::middleware('organization.role:Admin,Staf Operasional')
                ->group(function () {

                    // Venue
                    Route::get('/venues', [VenueController::class, 'index'])
                        ->name('venues.index');

                    Route::get('/venues/create', [VenueController::class, 'create'])
                        ->name('venues.create');

                    Route::post('/venues', [VenueController::class, 'store'])
                        ->name('venues.store');

                    Route::get('/venues/{venue}/edit', [VenueController::class, 'edit'])
                        ->name('venues.edit');

                    Route::put('/venues/{venue}', [VenueController::class, 'update'])
                        ->name('venues.update');


                    // Pengelolaan Acara
                    Route::get('/events', [EventController::class, 'index'])
                        ->name('events.index');

                    Route::get('/events/create', [EventController::class, 'create'])
                        ->name('events.create');

                    Route::post('/events', [EventController::class, 'store'])
                        ->name('events.store');

                    Route::get('/events/{event}/edit', [EventController::class, 'edit'])
                        ->name('events.edit');

                    Route::put('/events/{event}', [EventController::class, 'update'])
                        ->name('events.update');


                    // Equipment
                    Route::get('/equipment', [EquipmentController::class, 'index'])
                        ->name('equipment.index');

                    Route::get('/equipment/create', [EquipmentController::class, 'create'])
                        ->name('equipment.create');

                    Route::post('/equipment', [EquipmentController::class, 'store'])
                        ->name('equipment.store');

                    Route::get('/equipment/{equipment}/edit', [EquipmentController::class, 'edit'])
                        ->name('equipment.edit');

                    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update'])
                        ->name('equipment.update');


                    // Equipment Categories
                    Route::get('/equipment-categories', [EquipmentCategoryController::class, 'index'])
                        ->name('equipment-categories.index');

                    Route::get('/equipment-categories/create', [EquipmentCategoryController::class, 'create'])
                        ->name('equipment-categories.create');

                    Route::post('/equipment-categories', [EquipmentCategoryController::class, 'store'])
                        ->name('equipment-categories.store');

                    Route::get('/equipment-categories/{equipmentCategory}/edit', [EquipmentCategoryController::class, 'edit'])
                        ->name('equipment-categories.edit');

                    Route::put('/equipment-categories/{equipmentCategory}', [EquipmentCategoryController::class, 'update'])
                        ->name('equipment-categories.update');


                    // Penugasan Peralatan Acara
                    Route::get('/event-equipment', [EventEquipmentController::class, 'index'])
                        ->name('event-equipment.index');

                    Route::get('/event-equipment/create', [EventEquipmentController::class, 'create'])
                        ->name('event-equipment.create');

                    Route::post('/event-equipment', [EventEquipmentController::class, 'store'])
                        ->name('event-equipment.store');

                    Route::get('/event-equipment/{eventEquipment}/edit', [EventEquipmentController::class, 'edit'])
                        ->name('event-equipment.edit');

                    Route::put('/event-equipment/{eventEquipment}', [EventEquipmentController::class, 'update'])
                        ->name('event-equipment.update');
                });
        });


    // ==========================================
    // UNDANGAN ORGANISASI
    // ==========================================

    Route::post(
        '/o/{organization:slug}/invitations',
        [OrganizationInvitationController::class, 'store']
    )->middleware([
        'organization.member',
        'organization.role:Admin',
    ])->name('organizations.invitations.store');

    Route::get(
        '/invitations/{token}',
        [OrganizationInvitationController::class, 'show']
    )->name('invitations.accept');

    Route::post(
        '/invitations/{token}',
        [OrganizationInvitationController::class, 'accept']
    )->name('invitations.accept.submit');

});

require __DIR__.'/auth.php';
