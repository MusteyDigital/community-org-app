<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\EventItemController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\PublicOrganizationController;
use App\Models\Member;
use App\Models\EventItem;
use App\Models\Announcement;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicOrganizationController::class, 'index']);
Route::get('/browse', [PublicOrganizationController::class, 'directory']);
Route::get('/org/{slug}', [PublicOrganizationController::class, 'show']);

Route::get('/dashboard', function () {
    $membership = Member::where('user_id', auth()->id())->where('status', 'approved')->first();
    if (! $membership) {
        return view('dashboard', [
            'noOrganization' => true,
            'memberCount' => 0,
            'upcomingEvents' => collect(),
            'pinnedAnnouncements' => collect(),
            'eventCount' => 0,
            'announcementCount' => 0,
        ]);
    }
    $orgId = $membership->organization_id;
    return view('dashboard', [
        'noOrganization' => false,
        'memberCount' => Member::where('organization_id', $orgId)->count(),
        'upcomingEvents' => EventItem::where('organization_id', $orgId)->where('event_date', '>=', now())->orderBy('event_date')->take(3)->get(),
        'pinnedAnnouncements' => Announcement::where('organization_id', $orgId)->where('is_pinned', true)->take(3)->get(),
        'eventCount' => EventItem::where('organization_id', $orgId)->count(),
        'announcementCount' => Announcement::where('organization_id', $orgId)->count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('members', MemberController::class)->except(['show']);
    Route::post('/members/{member}/approve', [MemberController::class, 'approve'])->name('members.approve');
    Route::post('/members/{member}/reject', [MemberController::class, 'reject'])->name('members.reject');
    Route::resource('events', EventItemController::class)->except(['show']);
    Route::resource('announcements', AnnouncementController::class)->except(['show']);
    Route::resource('contributions', App\Http\Controllers\ContributionController::class)->except(['show']);
    Route::get('/contributions/pay/start', [App\Http\Controllers\PaystackController::class, 'pay'])->name('paystack.pay');
    Route::post('/contributions/pay/initialize', [App\Http\Controllers\PaystackController::class, 'initialize'])->name('paystack.initialize');
    Route::get('/contributions/pay/callback', [App\Http\Controllers\PaystackController::class, 'callback'])->name('paystack.callback');
    Route::get('/organizations/payout', [App\Http\Controllers\OrganizationPayoutController::class, 'edit'])->name('organizations.payout.edit');
    Route::post('/organizations/payout/resolve', [App\Http\Controllers\OrganizationPayoutController::class, 'resolve'])->name('organizations.payout.resolve');
    Route::post('/organizations/payout', [App\Http\Controllers\OrganizationPayoutController::class, 'update'])->name('organizations.payout.update');
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/create', [OrganizationController::class, 'create'])->name('organizations.create');
    Route::post('/organizations', [OrganizationController::class, 'store'])->name('organizations.store');
    Route::post('/organizations/{organization}/join', [OrganizationController::class, 'join'])->name('organizations.join');
    Route::middleware('super_admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/organizations', [App\Http\Controllers\Admin\OrganizationApprovalController::class, 'index'])->name('organizations.index');
        Route::post('/organizations/{organization}/approve', [App\Http\Controllers\Admin\OrganizationApprovalController::class, 'approve'])->name('organizations.approve');
        Route::post('/organizations/{organization}/reject', [App\Http\Controllers\Admin\OrganizationApprovalController::class, 'reject'])->name('organizations.reject');
    });
});

require __DIR__.'/auth.php';



