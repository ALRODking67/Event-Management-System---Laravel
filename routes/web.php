
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
use App\Http\Controllers\FinancialTransactionController;
use App\Http\Controllers\VendorNegotiationController;
use App\Http\Controllers\StaffController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
})->name('home');

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

            // ==========================================
            // NEGOSIASI VENDOR - ADMIN, STAF, DAN VENDOR
            // Hak akses diperiksa di VendorNegotiationController
            // ==========================================

            Route::get('/vendor-negotiations', [VendorNegotiationController::class, 'index'])
                ->name('vendor-negotiations.index');

            Route::get('/vendor-negotiations/{eventVendor}', [VendorNegotiationController::class, 'show'])
                ->name('vendor-negotiations.show');

            Route::post('/vendor-negotiations/{eventVendor}/counter', [VendorNegotiationController::class, 'counter'])
                ->name('vendor-negotiations.counter');

            Route::post('/vendor-negotiations/{eventVendor}/offers/{offer}/respond', [VendorNegotiationController::class, 'respond'])
                ->name('vendor-negotiations.respond');

            Route::post(
                '/vendor-negotiations/{eventVendor}/decision-requests',
                [VendorNegotiationController::class, 'requestDecision']
            )->name('vendor-negotiations.decision-requests.store');

            Route::post(
                '/vendor-negotiations/{eventVendor}/decision-requests/{decisionRequest}/review',
                [VendorNegotiationController::class, 'reviewDecision']
            )->name('vendor-negotiations.decision-requests.review');

            // Dashboard organisasi
            Route::get('/dashboard', [DashboardController::class, 'organization'])
                ->middleware('verified')
                ->name('organizations.dashboard');

            // Workspace organisasi
            Route::get('/', [OrganizationController::class, 'workspace'])
                ->name('organizations.workspace');

            // ==========================================
            // MANAJEMEN PENGGUNA ORGANISASI - ADMIN SAJA
            // ==========================================

            Route::middleware('organization.role:Admin')->group(function () {

                Route::get('/users', [UserController::class, 'index'])
                    ->name('users.index');

                Route::get('/users/{member}/edit', [UserController::class, 'edit'])
                    ->name('users.edit');

                Route::put('/users/{member}', [UserController::class, 'update'])
                    ->name('users.update');

                Route::post('/invitations', [OrganizationInvitationController::class, 'store'])
                    ->name('organizations.invitations.store');

                Route::patch('/invitations/{invitation}/revoke', [UserController::class, 'revokeInvitation'])
                    ->name('users.invitation.revoke');
            });

            Route::get('/notifications/{notification}/open', function (
                string $organization,
                string $notification
            ) {
                $record = auth()->user()->notifications()->findOrFail($notification);

                $record->markAsRead();

                return redirect()->to(
                    $record->data['url'] ?? route('organizations.dashboard', [
                        'organization' => $organization,
                    ])
                );
            })->name('notifications.open');


            // ==========================================
            // FITUR ADMIN DAN STAF OPERASIONAL ORGANISASI
            // ==========================================

            Route::middleware('organization.role:Admin,Staf Operasional')
                ->group(function () {

                    // KELOLA STAF - ADMIN SAJA
                    Route::get('/staff', [StaffController::class, 'index'])
                        ->name('staff.index');

                    Route::get('/staff/create', [StaffController::class, 'create'])
                        ->name('staff.create');

                    Route::post('/staff', [StaffController::class, 'store'])
                        ->name('staff.store');

                    Route::get('/staff/{member}/edit', [StaffController::class, 'edit'])
                        ->name('staff.edit');

                    Route::put('/staff/{member}', [StaffController::class, 'update'])
                        ->name('staff.update');

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

                    // Vendor Organisasi
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

                    
                    // Penugasan Vendor
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

                    // VENDOR CONFIRMATION
                    Route::get('/vendor-confirmations', [VendorConfirmationController::class, 'index'])
                        ->name('vendor-confirmations.index');

                    Route::get('/vendor-confirmations/create', [VendorConfirmationController::class, 'create'])
                        ->name('vendor-confirmations.create');

                    Route::post('/vendor-confirmations', [VendorConfirmationController::class, 'store'])
                        ->name('vendor-confirmations.store');

                    // Evaluasi Acara
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

                    
                    // MANAJEMEN KEUANGAN
                    Route::get('/financial-transactions', [FinancialTransactionController::class, 'index'])
                        ->name('financial-transactions.index');

                    Route::get('/financial-transactions/create', [FinancialTransactionController::class, 'create'])
                        ->name('financial-transactions.create');

                    Route::post('/financial-transactions', [FinancialTransactionController::class, 'store'])
                        ->name('financial-transactions.store');

                    Route::get('/financial-transactions/{transaction}/edit', [FinancialTransactionController::class, 'edit'])
                        ->whereNumber('transaction')
                        ->name('financial-transactions.edit');

                    Route::put('/financial-transactions/{transaction}', [FinancialTransactionController::class, 'update'])
                        ->whereNumber('transaction')
                        ->name('financial-transactions.update');

                    Route::delete('/financial-transactions/{transaction}', [FinancialTransactionController::class, 'destroy'])
                        ->whereNumber('transaction')
                        ->name('financial-transactions.destroy');

                    // ==========================================
                    // LAPORAN
                    // ==========================================

                    Route::get('/reports', [ReportController::class, 'index'])
                        ->name('reports.index');

                    
                    // KONSEP ACARA
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


                    // RUNDOWN ACARA
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


                });
        });


    // ==========================================
    // UNDANGAN ORGANISASI
    // ==========================================


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

